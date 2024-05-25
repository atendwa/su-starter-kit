<?php

namespace App\Api\Mpesa\Core;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SendRequest
{
    public static function index($url, $body, $paymentKey = false): Collection
    {
        $credentials = [
            true => [
                'key' => config('services.mpesa.payment_consumer_key'),
                'secret' => config('services.mpesa.payment_consumer_secret'),
            ],
            false => [
                'key' => config('services.mpesa.consumer_key'),
                'secret' => config('services.mpesa.consumer_secret'),
            ],
        ];

        $key = $credentials[$paymentKey]['key'];

        $secret = $credentials[$paymentKey]['secret'];

        $accessToken = GenerateAccessToken::index($key, $secret);

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($url, $body)
            ->json();

        return FetchAttributes::index($response);
    }
}
