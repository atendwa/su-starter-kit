<?php

namespace App\Api\Mpesa\Actions;

use App\Api\Mpesa\Core\CreateMpesaTransaction;
use App\Api\Mpesa\Core\FormatPhoneNumber;
use App\Api\Mpesa\Core\GenerateSecurityCredential;
use App\Api\Mpesa\Core\SendRequest;
use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use Illuminate\Support\Collection;

class InitiateB2C extends Controller
{
    private Collection $attributes;

    public function index(
        int $amount,
        string $number,
        ?string $remarks = null,
        ?string $callback = null,
        ?string $occassion = null
    ): MpesaTransaction {
        $callback = $callback ?? config('services.mpesa.b2c_result_url');

        $number = FormatPhoneNumber::index($number);

        $remarks = $remarks ?? 'Wallet Withdraw';

        $this->attributes = SendRequest::index(
            'https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest',
            [
                'InitiatorName' => config('services.mpesa.initiator_name'),
                'QueueTimeOutURL' => config('services.mpesa.b2c_timeout_url'),
                'SecurityCredential' => GenerateSecurityCredential::index(),
                'PartyA' => config('services.mpesa.payment_shortcode'),
                'CommandID' => 'BusinessPayment',
                'Occassion' => $occassion,
                'ResultURL' => $callback,
                'Remarks' => $remarks,
                'Amount' => $amount,
                'PartyB' => $number,
            ],
            true
        );

        return $this->storeResult($amount, $number);
    }

    private function storeResult($amount, $number): MpesaTransaction
    {
        $attributes = $this->attributes;

        $transaction = CreateMpesaTransaction::index($attributes);

        $transaction->phone_number = $number;

        $transaction->amount = $amount;

        $transaction->type = 'B2C';

        return $transaction;
    }
}
