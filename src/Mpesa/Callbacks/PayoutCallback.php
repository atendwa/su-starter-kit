<?php

namespace App\Api\Mpesa\Callbacks;

use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use App\Models\Payout;
use Illuminate\Http\Request;

class PayoutCallback extends Controller
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

        $stkPush->result_code = $response['ResultCode'];

        $stkPush->result_description = $response['ResultDesc'];

        if ($stkPush->result_code !== 0) {
            $stkPush->save();

            return;
        }

        $this->updateMpesaTransaction($stkPush, $response);

        $this->updatePayout($stkPush);
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

    private function updatePayout(MpesaTransaction $stkPush): void
    {
        $requestId = $stkPush->merchant_request_id;

        $payouts = Payout::whereMerchantRequestId($requestId)
            ->whereCheckoutRequestId($stkPush->checkout_request_id)
            ->get();

        if (! $payouts) {
            return;
        }

        foreach ($payouts as $payout) {
            $payout->is_successful = true;

            $payout->mpesa_receipt = $stkPush->mpesa_receipt_number;

            $payout->save();
        }
    }
}
