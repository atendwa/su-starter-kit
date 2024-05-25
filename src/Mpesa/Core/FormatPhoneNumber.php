<?php

namespace App\Api\Mpesa\Core;

class FormatPhoneNumber
{
    public static function index($phoneNumber): string
    {
        return '254'.mb_substr($phoneNumber, 1);
    }
}
