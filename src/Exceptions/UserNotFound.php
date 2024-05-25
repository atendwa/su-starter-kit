<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Exceptions;

use Exception;

class UserNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct('User not found.');
    }
}
