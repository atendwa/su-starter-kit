<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Commands;

//use Exception;
use Atendwa\SuStarterKit\Actions\PostInstallScript\UpdatePostInstallConfig;
use Exception;
use Illuminate\Console\Command;
//use function Laravel\Prompts\confirm;

final class InstallSuStarterKit extends Command
{
    protected $signature = 'su-starter-kit:install';

    protected $description = 'Install the su starter kit package';

    public function handle(): void
    {
        $this->info('Installing su starter kit...');

        $this->call('vendor:publish', [
            '--provider' => "Atendwa\SuStarterKit\SuStarterKitServiceProvider",
            '--tag' => 'config',
            '--force' => true,
        ]);

        try {
            $this->info('Running post install script...');

            $this->call('su-starter-kit:post-install-script');

            $this->info((new UpdatePostInstallConfig)->execute('post_install_script_executed'));
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }

//        $publishConfig = confirm('Do you want to publish the configuration file?');

//        if ($publishConfig) {

//        }

//        $publishMigration = confirm('Do you want to publish the migration files?');

//        if ($publishMigration) {
            $this->call('vendor:publish', [
                '--provider' => "Atendwa\SuStarterKit\SuStarterKitServiceProvider",
                '--tag' => 'migration',
                '--force' => true,
            ]);
//        }

//        if (confirm('Do you want to run migrations?')) {
            $this->call('db:wipe');

            $this->call('migrate');
//        }

        $this->info((new UpdatePostInstallConfig)->execute('package_install_script_executed'));

//        return;
//
//        try {
//            $seedDepartments = confirm('Do you want to seed departments?');
//
//            if ($seedDepartments) {
//                $this->call('su-starter-kit:seed-departments');
//            }
//
//            $this->info('Su starter kit installed successfully.');
//        } catch (Exception $exception) {
//            $this->info($exception->getMessage());
//        }
//
//        $this->call('install:broadcasting');
    }
}
