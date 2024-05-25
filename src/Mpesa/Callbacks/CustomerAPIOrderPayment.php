<?php

namespace App\Api\Mpesa\Callbacks;

use App\Api\Sms\SendSms;
use App\Http\Controllers\Controller;
use App\Models\BusinessDelivery\Order;
use App\Models\MpesaTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerAPIOrderPayment extends Controller
{
    public function __invoke(Request $request): void
    {
        $response = json_decode($request->getContent(), true);

        $response = $response['Body']['stkCallback'];

        $CheckoutRequestID = $response['CheckoutRequestID'];

        $MerchantRequestID = $response['MerchantRequestID'];

        $stkPush = MpesaTransaction::whereCheckoutRequestId($CheckoutRequestID)
            ->whereMerchantRequestId($MerchantRequestID)
            ->first();

        if (! $stkPush || $stkPush->is_complete) {
            return;
        }

        $stkPush->result_code = $response['ResultCode'];

        $stkPush->result_description = $response['ResultDesc'];

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

        $stkPush->transaction_date = now();

        $stkPush->is_complete = true;

        $stkPush->save();

        $this->updateOrder($stkPush);
    }

    private function updateOrder(MpesaTransaction $stkPush): void
    {
        $order = Order::with(['shippingInfo', 'user'])->find($stkPush->parent_record_id);

        $order->payment_reference = $stkPush->mpesa_receipt_number;

        $order->status = 'Payment received';

        $order->payment_completed_at = now();

        $order->save();

        $store = $order->user;

        $this->updateStoreWallet($store, $order);

        try {
            $this->sendNotifications($store, $order);

            //        todo:
            $message = 'Payment received! Your funds are secure in a '.
                'holding account until you receive the order.'.
                " We'll notify you once the order is out for delivery.";

            SendSms::index($order->shippingInfo->phone_number, $message);
        } catch (Exception $e) {
            Log::error($e);
        }

        $storeCallback = $store->businessCallbacks ?? null;

        if ($storeCallback?->payment_callback) {
            try {
                $data = [
                    'message' => 'The order has been paid for.',
                    'order_reference' => $order->order_reference,
                ];

                Http::post($storeCallback->payment_callback, $data);
            } catch (Exception $exception) {
                Log::info($exception);
            }
        }
    }

    private function updateStoreWallet(User $store, Order $order): void
    {
        $wallet = Wallet::whereUserId($store->id)->first();

        $wallet->incrementHolding($order->total_amount);

        $this->createWalletTransaction($store, $order, $wallet->id);
    }

    private function createWalletTransaction(
        User $sender,
        Order $order,
        int $id
    ): void {
        $transaction = new WalletTransaction();

        $transaction->caption = 'Order reference number:'.$order->order_reference;

        $transaction->amount = $order->total_amount;

        $transaction->slug = $transaction->generateSlug();

        $transaction->order_reference = $order->order_reference;

        $transaction->transaction_type = 'Wallet';

        $transaction->user_slug = $sender->slug;

        $transaction->title = 'Order Payment';

        $transaction->type = 'Package Payment';

        $transaction->user_id = $sender->id;

        $transaction->is_successful = true;

        $transaction->is_inbound = true;

        $transaction->wallet_id = $id;

        $transaction->save();
    }

    private function sendNotifications(User $store, Order $order): void
    {
        $message = $this->loadMessage($order);

        SendSms::index($store->phone_number, $message);
    }

    private function loadMessage(Order $order): string
    {
        return 'Order payment received for order with reference: '.$order->order_reference;

        //        AlertAdmin::create(['0711637583', '0706336597'], 'Schedule.');
    }
}
