<?php

namespace App\Api\Mpesa\Core;

use Illuminate\Support\Carbon;

class GeneratePassword
{
    public static function index(): string
    {
        return base64_encode(
            config('services.mpesa.shortcode').
            config('services.mpesa.pass_key').
            Carbon::rawParse('now')->format('YmdHms')
        );
    }
}
