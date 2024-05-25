<?php

namespace Atendwa\SuStarterKit\Commands;

use Atendwa\SuStarterKit\Actions\PostInstallScript\UpdateComposerFile;
use Atendwa\SuStarterKit\Actions\PostInstallScript\UpdatePostInstallConfig;
use Atendwa\SuStarterKit\Actions\PostInstallScript\UpdatePulseConfig;
use Atendwa\SuStarterKit\Actions\PostInstallScript\UpdatePulseDashboard;
use Atendwa\SuStarterKit\Actions\PostInstallScript\UpdatePulseOutdatedConfig;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class PostInstallScript extends Command
{
    protected $signature = 'su-starter-kit:post-install-script';

    protected $description = 'Command description';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->info((new UpdateComposerFile)->execute());

        $this->call('horizon:install');

        $this->call('telescope:install');

        $providers = [
            "Barryvdh\Debugbar\ServiceProvider",
            "Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider",
            "Spatie\Permission\PermissionServiceProvider",
            "Spatie\Activitylog\ActivitylogServiceProvider",
            "Laravel\Pulse\PulseServiceProvider"
        ];

        collect($providers)->each(function ($provider) {
            $this->call('vendor:publish', ['--provider' => $provider,]);
        });

        $this->info((new UpdatePulseOutdatedConfig)->execute());

        $this->info((new UpdatePulseDashboard)->execute());

        $this->info((new UpdatePulseConfig)->execute());
    }
}
