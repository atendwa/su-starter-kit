<?php

namespace App\Api\Mpesa\Actions;

use App\Api\Mpesa\Core\CreateMpesaTransaction;
use App\Api\Mpesa\Core\FormatPhoneNumber;
use App\Api\Mpesa\Core\GeneratePassword;
use App\Api\Mpesa\Core\SendRequest;
use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class InitiateSTKPush extends Controller
{
    private Collection $attributes;

    public function index(
        int $amount,
        string $phoneNumber,
        string $description,
        ?string $callback = null
    ): MpesaTransaction {
        $callback = $callback ?? config('services.mpesa.package_stk_callback');

        $phoneNumber = FormatPhoneNumber::index($phoneNumber);

        $this->attributes = SendRequest::index(
            'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            [
                'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
                'BusinessShortCode' => config('services.mpesa.shortcode'),
                'AccountReference' => config('services.mpesa.shortcode'),
                'PartyB' => config('services.mpesa.shortcode'),
                'TransactionType' => 'CustomerPayBillOnline',
                'Password' => GeneratePassword::index(),
                'TransactionDesc' => $description,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => $callback,
                'PartyA' => $phoneNumber,
                'Amount' => $amount,
            ],
        );

        return $this->storeResult($amount, $phoneNumber);
    }

    private function storeResult($amount, $phoneNumber): MpesaTransaction
    {
        $attributes = $this->attributes;

        $transaction = CreateMpesaTransaction::index($attributes);

        $transaction->phone_number = $phoneNumber;

        $transaction->amount = $amount;

        $transaction->type = 'STKPUSH';

        return $transaction;
    }
}
