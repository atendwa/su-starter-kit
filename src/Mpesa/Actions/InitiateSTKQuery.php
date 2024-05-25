<?php

namespace App\Api\Mpesa\Actions;

use App\Api\Mpesa\Core\GeneratePassword;
use App\Api\Mpesa\Core\SendRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class InitiateSTKQuery extends Controller
{
    public function index($checkoutRequestId): Collection
    {
        return SendRequest::index(
            'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query',
            [
                'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
                'BusinessShortCode' => config('services.mpesa.shortcode'),
                'CheckoutRequestID' => $checkoutRequestId,
                'Password' => GeneratePassword::index(),
            ],
        );
    }
}
