<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Exceptions;

use Exception;

class DepartmentNotFound extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'No Department found.');
    }
}
