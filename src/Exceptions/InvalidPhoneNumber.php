<?php

declare(strict_types=1);
namespace Atendwa\MpesaArtisan\Exceptions;

use Exception;

class InvalidPhoneNumber extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Invalid Phone Number Provided.');
    }
}
