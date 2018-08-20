<?php

namespace Bmadeiro\LaravelProject;

use Illuminate\Support\ServiceProvider;

class LaravelProjectServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/generator.php' => config_path('generator.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../publish/views/' => base_path('resources/views/'),
        ]);

        if (\App::VERSION() <= '5.2') {
            $this->publishes([
                __DIR__ . '/../publish/css/app.css' => public_path('css/app.css'),
            ]);
        }

        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/generator/'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Bmadeiro\LaravelProject\Commands\CrudCommand',
            'Bmadeiro\LaravelProject\Commands\CrudControllerCommand',
            'Bmadeiro\LaravelProject\Commands\CrudModelCommand',
            'Bmadeiro\LaravelProject\Commands\CrudMigrationCommand',
            'Bmadeiro\LaravelProject\Commands\CrudViewCommand',
            'Bmadeiro\LaravelProject\Commands\CrudLangCommand',
            'Bmadeiro\LaravelProject\Commands\CrudApiCommand',
            'Bmadeiro\LaravelProject\Commands\CrudApiControllerCommand'
        );
    }
}
