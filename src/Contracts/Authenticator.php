<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Contracts;

interface Authenticator
{
    public function authenticate(): bool;
}
