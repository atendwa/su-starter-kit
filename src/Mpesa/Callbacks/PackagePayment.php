<?php

namespace App\Api\Mpesa\Callbacks;

use App\Api\Sms\SendSms;
use App\Helpers\Notification\AlertAdmin;
use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use App\Models\Package;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class PackagePayment extends Controller
{
    public function __invoke(Request $request): void
    {
        $response = json_decode($request->getContent(), true);

        $response = $response['Body']['stkCallback'];

        $CheckoutRequestID = $response['CheckoutRequestID'];

        $stkPush = MpesaTransaction::whereCheckoutRequestId($CheckoutRequestID)
            ->whereMerchantRequestId($response['MerchantRequestID'])
            ->first();

        if (! $stkPush || $stkPush->is_complete) {
            return;
        }

        $stkPush->result_description = $response['ResultDesc'];

        $stkPush->result_code = $response['ResultCode'];

        if ($response['ResultCode'] !== 0) {
            $stkPush->save();

            return;
        }

        $this->updateMpesaTransaction($stkPush, $response);
    }

    private function updateMpesaTransaction(
        MpesaTransaction $stkPush,
        array $response
    ): void {
        $response = $response['CallbackMetadata']['Item'];

        $stkPush->mpesa_receipt_number = $response[1]['Value'];

        //        $timestamp = Carbon::createFromTimestamp($response[3]['Value']) ?? now()->timestamp;

        $stkPush->transaction_date = now();

        $stkPush->is_complete = true;

        $stkPush->save();

        $this->updatePackage($stkPush);
    }

    private function updatePackage(MpesaTransaction $stkPush): void
    {
        $package = Package::find($stkPush->parent_record_id);

        $package->status = 'Package payment received';

        $package->time_payment_attempted = now();

        $package->time_paid_for = now();

        $package->save();

        $sender = User::find($package->sender_id);

        $this->updateSenderWallet($sender, $package);

        $this->sendNotifications($sender, $package);

        $message = 'Payment received! Your funds are secure in a '.
            'holding account until you receive the package and'.
            " share the OTP. We'll notify you once the package is picked up.";

        SendSms::index($package->recipient_phone_number, $message);
    }

    private function updateSenderWallet(User $sender, Package $package): void
    {
        $wallet = Wallet::whereUserId($sender->id)->first();

        $wallet->incrementHolding($package->amount_payable_to_seller);

        $this->createWalletTransaction($sender, $package, $wallet->id);
    }

    private function createWalletTransaction(
        User $sender,
        Package $package,
        int $id
    ): void {
        $transaction = new WalletTransaction();

        $transaction->caption = 'Package tracking number:'.$package->slug;

        $transaction->amount = $package->amount_payable_to_seller;

        $transaction->slug = $transaction->generateSlug();

        $transaction->package_slug = $package->slug;

        $transaction->transaction_type = 'Wallet';

        $transaction->user_slug = $sender->slug;

        $transaction->title = 'Package Payment';

        $transaction->type = 'Package Payment';

        $transaction->user_id = $sender->id;

        $transaction->is_successful = true;

        $transaction->is_inbound = true;

        $transaction->wallet_id = $id;

        $transaction->save();
    }

    private function sendNotifications(User $sender, Package $package): void
    {
        $message = $this->loadMessage($package);

        SendSms::index($sender->phone_number, $message);
    }

    private function loadMessage(Package $package): string
    {
        if ($package->drop_off_point_id) {
            $message = 'Payment received! Kindly drop off the package.';

            $message .= 'The tracking ID is ['.$package->slug
                .'] . Use this OTP ('.$package->sender_otp.
                ') to complete the drop-off.';

            return $message;
        }

        AlertAdmin::create(['0711637583', '0706336597'], 'Schedule.');

        return 'Payment received! Prepare the package for pickup.';
    }
}
