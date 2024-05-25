<?php

namespace App\Api\Mpesa\Actions;

use App\Api\Mpesa\Core\GenerateSecurityCredential;
use App\Api\Mpesa\Core\SendRequest;
use Illuminate\Support\Collection;

class FetchAccountBalance
{
    public function index(): Collection
    {
        return SendRequest::index(
            'https://api.safaricom.co.ke/mpesa/accountbalance/v1/query',
            [
                'IdentifierType' => 4,
                //1 – MSISDN 2 – Till Number 4 – Organization short code
                'QueueTimeOutURL' => config('daraja.balance_timeout_url'),
                'SecurityCredential' => GenerateSecurityCredential::index(),
                'ResultURL' => config('daraja.balance_result_url'),
                'Initiator' => config('daraja.initiator_name'),
                'PartyA' => config('daraja.payment_shortcode'),
                'CommandID' => 'AccountBalance',
                'Remarks' => 'Get Balance',
            ]
        );
    }
}
