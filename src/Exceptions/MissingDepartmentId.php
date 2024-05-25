<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Exceptions;

use Exception;

class MissingDepartmentId extends Exception
{
    public function __construct()
    {
        parent::__construct('Department ID is required.');
    }
}
