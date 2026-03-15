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
        // broadcasting driver register
        $this->app->afterResolving(BroadcastManager::class, function ($manager) {
            $manager->extend('comnestor', function ($app, $config) {
                return new ComnestorBroadcaster($config);
            });
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class
            ]);
        }
    }
}