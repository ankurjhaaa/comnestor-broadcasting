<?php

namespace Ankurjha\Comnestor\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{

    protected $signature = 'comnestor:install';

    protected $description = 'Install Comnestor broadcasting driver';

    public function handle()
    {

        $this->info('Installing Comnestor broadcasting...');

        $this->createServiceFile();

        $this->updateBroadcastConfig();

        $this->info('Comnestor installed successfully');

    }

    protected function createServiceFile()
    {

        $path = app_path('Services/ComnestorBroadcasting.php');

        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path),0755,true);
        }

        $stub = file_get_contents(__DIR__.'/../Stubs/ComnestorBroadcasting.stub');

        File::put($path,$stub);

        $this->info('Created Service file');

    }

    protected function updateBroadcastConfig()
    {

        $config = config_path('broadcasting.php');

        $content = file_get_contents($config);

        if (str_contains($content,'comnestor')) {
            return;
        }

        $insert = "

        'comnestor' => [
            'driver' => 'comnestor',
            'base_url' => env('COMNESTOR_BASE_URL'),
            'app_key' => env('COMNESTOR_APP_KEY'),
            'app_secret' => env('COMNESTOR_APP_SECRET'),
        ],
        ";

        $content = str_replace("'connections' => [","'connections' => [".$insert,$content);

        file_put_contents($config,$content);

        $this->info('Broadcast driver added');

    }

}