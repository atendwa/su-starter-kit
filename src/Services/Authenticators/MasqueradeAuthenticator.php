<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Services\Authenticators;

use Atendwa\SuStarterKit\Contracts\Authenticator;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MasqueradeAuthenticator implements Authenticator
{
    /**
     * @throws Throwable
     */
    public function authenticate(): bool
    {
        $message = 'Masquerade is not allowed in production!';

        throw_if(app()->isProduction(), $message);

        $username = config('authentication.drivers.masquerade.username');

        throw_if(! $username, 'Masquerade username is not set!');

        $user = get_user_by_username($username);

        throw_if(! $user, 'Masquerade user not found!');

        Auth::loginUsingId($user->id, true);

        return Auth::check();
    }
}
