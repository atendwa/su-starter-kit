<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Observers;

use Atendwa\SuStarterKit\Models\User;
use Atendwa\SuStarterKit\Support\AttributeSanitizers\UserSanitizer;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        self::formatAttributes($user);
    }

    public function updating(User $user): void
    {
        self::formatAttributes($user);
    }

    private static function formatAttributes(User $user): void
    {
        $sanitiser = new UserSanitizer();

        $user->email = Str::lower($user->email);

        $user->username = Str::lower($user->username);

        $user->first_name = Str::title(Str::lower($user->first_name));

        $user->last_name = Str::title(Str::lower($user->last_name));

        $user->other_names = $sanitiser->otherNames($user->other_names);

        $name = $user->first_name . ' ' . $user->other_names;

        $name .= ' ' . $user->last_name;

        $user->name = Str::title(str_replace('  ', ' ', $name));

        $user->phone_number = $sanitiser->phoneNumber($user->phone_number);
    }
}
