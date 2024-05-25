<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Support\AttributeSanitizers;

use Illuminate\Support\Str;

class UserSanitizer
{
    public function phoneNumber(?string $phoneNumber): ?string
    {
        if (! $phoneNumber) {
            return null;
        }

        $phoneNumber = Str::replace('+254', '0', $phoneNumber);
        $phoneNumber = Str::replace('254', '0', $phoneNumber);
        $phoneNumber = Str::replace(' ', '', $phoneNumber);
        $phoneNumber = Str::replace('-', '', $phoneNumber);
        $phoneNumber = Str::remove('(0)', $phoneNumber);

        return Str::trim($phoneNumber);
    }

    public function otherNames(?string $otherNames): ?string
    {
        if (mb_strlen($otherNames ?? '')) {
            return Str::title(Str::lower(Str::trim($otherNames)));
        }

        return null;
    }
}
