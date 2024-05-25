<?php

declare(strict_types=1);

namespace Atendwa\MpesaArtisan\Commands;

use Illuminate\Console\Command;

//use function Laravel\Prompts\confirm;

final class InstallMpesaArtisan extends Command
{
    protected $signature = 'mpesa-artisan:install';

    protected $description = '';

    public function handle(): void
    {
        info('Installing mpesa-artisan...');

        $params = [
            '--provider' => "Atendwa\MpesaArtisan\MpesaArtisanServiceProvider",
            '--tag' => 'config',
            '--force' => 'true',
        ];

        $this->call('vendor:publish', $params);
    }
}
