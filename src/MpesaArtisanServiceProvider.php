<?php

declare(strict_types=1);

namespace Atendwa\MpesaArtisan;

use Atendwa\MpesaArtisan\Commands\InstallMpesaArtisan;
use Illuminate\Support\ServiceProvider;

final class MpesaArtisanServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands(InstallMpesaArtisan::class);

            $this->publishes([
                __DIR__ . '/../config/mpesa.php' => config_path('mpesa.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migration');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mpesa.php', 'mpesa');
    }
}
