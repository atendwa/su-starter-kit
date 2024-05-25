<?php

namespace App\Api\Mpesa\Callbacks;

use App\Api\Sms\SendSms;
use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletWithdrawal extends Controller
{
    public function __invoke(Request $request): void
    {
        $response = json_decode($request->getContent(), true)['Result'];

        $oid = $response['OriginatorConversationID'];

        $cid = $response['ConversationID'];

        $b2c = MpesaTransaction::whereConversationId($cid)
            ->whereOriginatorConversationId($oid)
            ->first();

        if (! $b2c || $b2c->is_complete) {
            return;
        }

        $b2c->mpesa_receipt_number = $response['TransactionID'];

        $b2c->result_type = $response['ResultType'];

        $b2c->result_description = $response['ResultDesc'];

        $b2c->result_code = $response['ResultCode'];

        if ($b2c->result_code !== 0) {
            $b2c->save();

            return;
        }

        $this->updateRecords($b2c, $response);
    }

    public function updateRecords(
        MpesaTransaction $b2c,
        array $response
    ): void {
        $response = $response['ResultParameters']['ResultParameter'];

        $b2c->charges_paid_account_available_funds = $response[7]['Value'];

        $b2c->utility_account_available_funds = $response[4]['Value'];

        $b2c->working_account_available_funds = $response[5]['Value'];

        $b2c->receiver_party_public_name = $response[2]['Value'];

        $b2c->registered_customer = $response[6]['Value'];

        $b2c->mpesa_receipt_number = $response[1]['Value'];

        $b2c->amount = $response[0]['Value'];

        $b2c->transaction_date = now();

        $b2c->is_complete = true;

        $b2c->save();

        $this->updateWalletTransaction($b2c);
    }

    private function updateWalletTransaction(MpesaTransaction $b2c): void
    {
        $ocid = $b2c->originator_conversation_id;

        $transaction = WalletTransaction::whereOriginatorConversationId($ocid)
            ->whereConversationId($b2c->conversation_id)
            ->first();

        $transaction->mpesa_receipt = $b2c->mpesa_receipt_number;

        $transaction->is_successful = true;

        $transaction->save();

        $this->updateWallet($transaction);
    }

    private function updateWallet(WalletTransaction $walletTransaction): void
    {
        $wallet = Wallet::find($walletTransaction->wallet_id);

        $wallet?->deductBalance($walletTransaction->amount);

        $this->sendNotification($wallet, $walletTransaction->amount);
    }

    private function sendNotification(Wallet $wallet, int $amount): void
    {
        $number = User::find($wallet->user_id)->phone_number;

        $message = 'Your withdrawal request of Ksh.'.
            number_format($amount).
            ' has been successfully processed.';

        SendSms::index($number, $message);
    }
}
