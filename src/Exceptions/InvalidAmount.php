<?php

declare(strict_types=1);
namespace Atendwa\MpesaArtisan\Exceptions;

use Exception;

class InvalidAmount extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Invalid amount provided. Amount should be a number > 0.');
    }
}
