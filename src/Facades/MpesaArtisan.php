<?php

declare(strict_types=1);

namespace Atendwa\MpesaArtisan\Facades;

use Illuminate\Support\Facades\Facade;

class MpesaArtisan extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Atendwa\MpesaArtisan\MpesaArtisan::class;
    }
}
