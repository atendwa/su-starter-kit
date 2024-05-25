<?php

namespace Atendwa\MpesaArtisan\Support;

use Atendwa\MpesaArtisan\Exceptions\InvalidPhoneNumber;
use Illuminate\Support\Str;
use Throwable;

class SanitisePhoneNumber
{
    /**
     * @throws Throwable
     */
    public static function index(string $phoneNumber): string
    {
        if (Str::startsWith($phoneNumber, '+')) {
            $phoneNumber = str_replace('+', '', $phoneNumber);
        }

        if (Str::startsWith($phoneNumber, '254')) {
            $phoneNumber = str_replace('254', '', $phoneNumber);
        }

        if ($phoneNumber[0] === '0') {
            $phoneNumber = mb_substr($phoneNumber, 1);
        }

        throw_if(mb_strlen($phoneNumber) !== 9, new InvalidPhoneNumber());

        return '254' . $phoneNumber;
    }
}
