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

        $this->updateBroadcastConfig();

        $this->info('Comnestor broadcasting driver installed successfully');

    }

    

    protected function updateBroadcastConfig()
    {
        $config = config_path('broadcasting.php');

        // agar broadcasting config exist nahi karta
        if (!file_exists($config)) {

            $this->info('Broadcasting config not found. Creating...');

            $stub = __DIR__ . '/../Stubs/broadcasting.stub';

            if (file_exists($stub)) {
                copy($stub, $config);
            } else {

                // minimal fallback config
                file_put_contents($config, "<?php

return [

    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [

    ],

];
");
            }
        }

        $content = file_get_contents($config);

        if (str_contains($content, "'comnestor'")) {
            $this->info('Comnestor driver already exists.');
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

        $content = str_replace(
            "'connections' => [",
            "'connections' => [" . $insert,
            $content
        );

        file_put_contents($config, $content);

        $this->info('Comnestor driver added successfully.');
    }

}