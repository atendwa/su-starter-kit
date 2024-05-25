<?php

namespace Atendwa\SuStarterKit\Concerns;

trait ErrorParser
{
    private function parseError(string $error, ?string $default = null): string
    {
        $message = $default ?? $this->default();

        return app()->isProduction() ? $message : $error;
    }

    private function default(): string
    {
        return 'Something went wrong. Please try again later.';
    }
}
