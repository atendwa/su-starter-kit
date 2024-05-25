<?php

namespace Atendwa\SuStarterKit\Actions\PostInstallScript;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class UpdatePulseDashboard
{
    /**
     * @throws FileNotFoundException
     */
    public function execute(): string
    {
        $fs = new Filesystem;

        $dashboardBladePath = resource_path('views/vendor/pulse/dashboard.blade.php');

        if ($fs->missing($dashboardBladePath)) {
            return 'dashboard.blade.php not found.';
        }

        $dashboardBlade = $fs->get($dashboardBladePath);

        $newLines = [
            '<livewire:reverb.connections cols="full" />',
            '<livewire:reverb.messages cols="full" />',
            '<livewire:outdated cols="4" rows="2" />',
            '<livewire:pulse.validation-errors cols="8" rows="4" />',
        ];

        $linesToAdd = array_filter($newLines, function($line) use ($dashboardBlade) {
            return !str_contains($dashboardBlade, $line);
        });

        if (empty($linesToAdd)) {
            return 'dashboard.blade.php already contains the necessary lines.';
        }

        // Join the new lines and add them before the closing </x-pulse> tag
        $linesToAddString = implode("\n", $linesToAdd) . "\n";

        if (str_contains($dashboardBlade, '</x-pulse>')) {
            $replace =  $linesToAddString . '</x-pulse>';

            $updatedDashboardBlade = str_replace('</x-pulse>', $replace, $dashboardBlade);
        } else {
            $updatedDashboardBlade = $dashboardBlade . "\n" . $linesToAddString;
        }

        // Write the updated dashboard.blade.php file
        $fs->put($dashboardBladePath, $updatedDashboardBlade);

        return 'dashboard.blade.php updated successfully.';
    }
}
