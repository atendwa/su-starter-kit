<?php

namespace Atendwa\SuStarterKit\Actions\PostInstallScript;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class UpdatePulseOutdatedConfig
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
            \AaronFrancis\Pulse\Outdated\Recorders\OutdatedRecorder::class => [],
        EOT;

        if (!str_contains($pulseConfig, '\AaronFrancis\Pulse\Outdated\Recorders\OutdatedRecorder::class')) {
            // Add the configuration to the recorders section
            $search = "'recorders' => [";
            $replacement = "'recorders' => [\n".$configToAdd;
            $updatedPulseConfig = str_replace($search, $replacement, $pulseConfig);

            // Write the updated pulse.php file
            $fs->put($pulseConfigPath, $updatedPulseConfig);

            return 'pulse.php updated successfully.';
        }

        return 'pulse.php already contains the OutdatedRecorder configuration.';
    }
}
