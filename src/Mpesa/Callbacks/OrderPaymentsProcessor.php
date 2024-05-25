<?php

namespace App\Api\Mpesa\Callbacks;

use App\Models\MpesaTransaction;
use Illuminate\Http\Request;

class OrderPaymentsProcessor
{
    public function __invoke(Request $request): void
    {
        $response = json_decode($request->getContent(), true);

        $response = $response['Body']['stkCallback'];

        $id = $response['CheckoutRequestID'];

        $stkPush = MpesaTransaction::whereCheckoutRequestId($id)
            ->whereMerchantRequestId($response['MerchantRequestID'])
            ->first();

        if (! $stkPush || $stkPush->is_complete) {
            return;
        }

        $stkPush->result_description = $response['ResultDesc'];

        $stkPush->result_code = $response['ResultCode'];

        if ($stkPush->result_code !== 0) {
            $stkPush->save();

            return;
        }

        $this->updateMpesaTransaction($stkPush, $response);

        $this->updateOrderPayment($stkPush);
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
    }

    private function updateOrderPayment(MpesaTransaction $stkPush): void
    {
        $requestId = $stkPush->merchant_request_id;

        $orderPayments = \App\Models\BusinessDelivery\OrderPayment::whereMerchantRequestId($requestId)
            ->whereCheckoutRequestId($stkPush->checkout_request_id)
            ->get();

        if (! $orderPayments) {
            return;
        }

        foreach ($orderPayments as $orderPayment) {
            $orderPayment->is_successful = true;

            $orderPayment->mpesa_receipt = $stkPush->mpesa_receipt_number;
            $orderPayment->completed_at = now();

            $orderPayment->save();
        }
    }
}
