<?php

namespace Ankurjha\Comnestor;

use Illuminate\Support\ServiceProvider;
use Ankurjha\Comnestor\Console\InstallCommand;

class ComnestorServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                InstallCommand::class
            ]);
        }
    }
}