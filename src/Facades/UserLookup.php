<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Facades;

use Illuminate\Support\Facades\Facade;

class UserLookup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Atendwa\SuStarterKit\Services\DataService\UserLookup::class;
    }
}
