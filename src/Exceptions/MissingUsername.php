<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Exceptions;

use Exception;

class MissingUsername extends Exception
{
    public function __construct()
    {
        parent::__construct('Username is required.');
    }
}
