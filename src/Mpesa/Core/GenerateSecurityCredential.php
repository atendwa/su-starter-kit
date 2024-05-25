<?php

namespace App\Api\Mpesa\Core;

use Illuminate\Support\Facades\Storage;

class GenerateSecurityCredential
{
    public static function index(): string
    {
        openssl_public_encrypt(
            config('services.mpesa.security_credential'),
            $encrypted,
            Storage::disk('local')->get('ProductionCertificate.cer')
        );

        return base64_encode($encrypted);
    }
}
