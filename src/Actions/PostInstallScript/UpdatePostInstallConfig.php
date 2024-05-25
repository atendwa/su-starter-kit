<?php

namespace Atendwa\SuStarterKit\Actions\PostInstallScript;

use Illuminate\Support\Facades\File;

class UpdatePostInstallConfig
{
    public function execute(string $key): string
    {
        $configFile = config_path('starter_kit.php');

        if (File::missing($configFile)) {
            return 'Config file not found.';
        }

        $config = File::getRequire($configFile);
        $config[$key] = true;

        $exported = var_export($config, true);
        File::put($configFile, "<?php\n\nreturn $exported;\n");

        return 'Configuration updated successfully.';
    }
}
