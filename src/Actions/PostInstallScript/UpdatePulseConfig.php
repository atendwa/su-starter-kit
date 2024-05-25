<?php

namespace Atendwa\SuStarterKit\Actions\PostInstallScript;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class UpdatePulseConfig
{
    /**
     * @throws FileNotFoundException
     */
    public function execute(): string
    {
        $fs = new Filesystem;

        $pulseConfigPath = config_path('pulse.php');

        if ($fs->missing($pulseConfigPath)) {
            return 'pulse.php not found.';
        }

        $pulseConfig = $fs->get($pulseConfigPath);

        $configToAdd = <<<EOT
            TiMacDonald\Pulse\Recorders\ValidationErrors::class => [
                'enabled' => env('PULSE_VALIDATION_ERRORS_ENABLED', true),
                'sample_rate' => env('PULSE_VALIDATION_ERRORS_SAMPLE_RATE', 1),
                'capture_messages' => true,
                'ignore' => [
                    // '#^/login$#',
                    // '#^/register$#',
                    // '#^/forgot-password$#',
                ],
                'groups' => [
                    // '#^/products/.*$#' => '/products/{user}',
                ],
            ],
        EOT;

        if (!str_contains($pulseConfig, 'TiMacDonald\Pulse\Recorders\ValidationErrors::class')) {
            // Add the configuration to the recorders section
            $search = "'recorders' => [";
            $replacement = "'recorders' => [\n" . $configToAdd;
            $updatedPulseConfig = str_replace($search, $replacement, $pulseConfig);

            // Write the updated pulse.php file
            $fs->put($pulseConfigPath, $updatedPulseConfig);

            return 'pulse.php updated successfully.';
        }

        return 'pulse.php already contains the configuration.';
    }
}
