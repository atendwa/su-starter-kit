<?php

namespace App\Api\Mpesa\Callbacks;

use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletTopUp extends Controller
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

        $this->updateWalletTransaction($stkPush);
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
    }

    private function updateWalletTransaction(MpesaTransaction $stkPush): void
    {
        $requestId = $stkPush->merchant_request_id;

        $transaction = WalletTransaction::whereMerchantRequestId($requestId)
            ->whereCheckoutRequestId($stkPush->checkout_request_id)
            ->first();

        if (! $transaction) {
            return;
        }

        $transaction->is_successful = true;

        $transaction->save();

        $this->updateWallet($transaction);
    }

    private function updateWallet(WalletTransaction $transaction): void
    {
        $wallet = Wallet::find($transaction->wallet_id);

        $wallet?->incrementBalance($transaction->amount);
    }
}
