<?php

namespace Ankurjha\Comnestor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Broadcasting\BroadcastManager;
use Ankurjha\Comnestor\Broadcasting\ComnestorBroadcaster;
use Ankurjha\Comnestor\Console\InstallCommand;

class ComnestorServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->app->make(BroadcastManager::class)->extend(
            'comnestor',
            function ($app, $config) {
                return new ComnestorBroadcaster($config);
            }
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class
            ]);
        }
    }
}