<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Services\Authenticators;

use Atendwa\SuStarterKit\Contracts\Authenticator;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DatabaseAuthenticator implements Authenticator
{
    /**
     * @throws Throwable
     */
    public function authenticate(): bool
    {
        $credentials = [];

        $credentials['email'] = request()->input('email');

        $credentials['password'] = request()->input('password');

        return Auth::attempt($credentials, true);
    }
}
