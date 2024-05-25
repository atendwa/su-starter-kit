<?php

namespace App\Api\Mpesa\Actions;

use App\Api\Mpesa\Core\SendRequest;
use Illuminate\Support\Collection;

class RegisterUrls
{
    public function index(): Collection
    {
        return SendRequest::index(
            'https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl',
            [
                'ConfirmationURL' => config('daraja.c2b_confirmation_url'),
                'ValidationURL' => config('daraja.c2b_validation_url'),
                'ShortCode' => config('daraja.shortcode'),
                'ResponseType' => 'Completed',
            ]
        );
    }
}
