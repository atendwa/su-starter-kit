<?php

namespace Atendwa\SuStarterKit\Actions\PostInstallScript;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class UpdateComposerFile
{
    /**
     * @throws FileNotFoundException
     */
    public function execute(): string
    {
        $fs = new Filesystem;

        $composerJsonPath = base_path('composer.json');

        if ($fs->missing($composerJsonPath)) {
            return 'composer.json not found.';
        }

        $composerJson = json_decode($fs->get($composerJsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return 'Invalid composer.json.';
        }

        // Ensure 'scripts' and 'post-update-cmd' exist
        if (!isset($composerJson['scripts'])) {
            $composerJson['scripts'] = [];
        }

        if (!isset($composerJson['scripts']['post-update-cmd'])) {
            $composerJson['scripts']['post-update-cmd'] = [];
        }

        // Define the scripts to add
        $newScripts = [
            "Illuminate\\Foundation\\ComposerScripts::postUpdate",
            "@php artisan ide-helper:generate",
            "@php artisan ide-helper:meta"
        ];

        // Merge new scripts with existing ones, avoiding duplicates
        $composerJson['scripts']['post-update-cmd'] = array_unique(
            array_merge(
                $composerJson['scripts']['post-update-cmd'],
                $newScripts
            )
        );

        // Write the updated composer.json file

        $content = json_encode(
            $composerJson,
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );

        $fs->put($composerJsonPath, $content);

        return 'composer.json updated successfully.';
    }
}
