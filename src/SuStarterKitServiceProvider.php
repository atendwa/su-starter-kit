<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit;

use Atendwa\SuStarterKit\Commands\InstallSuStarterKit;
use Atendwa\SuStarterKit\Commands\SeedDepartments;
use Atendwa\SuStarterKit\Commands\PostInstallScript;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

final class SuStarterKitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->commands(SeedDepartments::class);

        if ($this->app->runningInConsole()) {
            // Publishing the configuration file.
            $this->publishes([
                __DIR__ . '/../config/authentication.php' => config_path('authentication.php'),
                __DIR__ . '/../config/data_service.php' => config_path('data_service.php'),
                __DIR__ . '/../config/starter_kit.php' => config_path('starter_kit.php'),
            ], 'config');

            // Publishing the migration files.
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migration');
        }

        if (!config('starter_kit.post_install_script_executed')) {
            $this->commands(PostInstallScript::class);
        }

        if (!config('starter_kit.package_install_script_executed')) {
            $this->commands(InstallSuStarterKit::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('DepartmentLookup', 'Atendwa\\SuStarterKit\\Facades\\DepartmentLookup');
        $loader->alias('BooleanEvaluator', 'Atendwa\\SuStarterKit\\Facades\\BooleanEvaluator');
        $loader->alias('UserLookup', 'Atendwa\\SuStarterKit\\Facades\\UserLookup');
        $loader->alias('ApiClient', 'Atendwa\\SuStarterKit\\Facades\\ApiClient');

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/authentication.php', 'authentication');
        $this->mergeConfigFrom(__DIR__ . '/../config/data_service.php', 'data_service');
        $this->mergeConfigFrom(__DIR__ . '/../config/starter_kit.php', 'starter_kit');
    }
}
