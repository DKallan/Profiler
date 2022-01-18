<?php

namespace Rosterbuster\Profiler;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class InfectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        foreach(Config::get('infection.classes') as $class) {
            $this->app->bind($class, function() use ($class) {
                return infect($class);
            });
        }
    }
}