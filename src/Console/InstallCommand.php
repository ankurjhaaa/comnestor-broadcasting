<?php

namespace Ankurjha\Comnestor\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'comnestor:install';

    protected $description = 'Install Comnestor broadcasting service';

    public function handle()
    {
        $this->createServiceFile();
        $this->updateServicesConfig();

        $this->info('Comnestor installed successfully.');
    }

    protected function createServiceFile()
    {
        $path = app_path('Services/ComnestorBroadcasting.php');

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }

        if (!File::exists($path)) {

            $stub = file_get_contents(__DIR__ . '/../Stubs/ComnestorBroadcasting.stub');

            File::put($path, $stub);

            $this->info('Service created: app/Services/ComnestorBroadcasting.php');
        }
    }

    protected function updateServicesConfig()
    {
        $configPath = config_path('services.php');

        $content = file_get_contents($configPath);

        if (str_contains($content, 'comnestor')) {
            return;
        }

        $insert = "

    'comnestor' => [
        'base_url' => env('COMNESTOR_BASE_URL'),
        'app_key' => env('COMNESTOR_APP_KEY'),
        'app_secret' => env('COMNESTOR_APP_SECRET'),
    ],
";

        $content = str_replace(
            '];',
            $insert . '];',
            $content
        );

        file_put_contents($configPath, $content);

        $this->info('Config added to services.php');
    }
}