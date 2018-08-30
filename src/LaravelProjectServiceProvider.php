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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Bmadeiro\LaravelProject\Commands\CreateCommand',
            'Bmadeiro\LaravelProject\Commands\CreateProjectCommand',
            'Bmadeiro\LaravelProject\Commands\CreateControllerCommand',
            'Bmadeiro\LaravelProject\Commands\CreateModelCommand',
            'Bmadeiro\LaravelProject\Commands\CreateMigrationCommand',
            'Bmadeiro\LaravelProject\Commands\CreateViewCommand'
        );
    }
}
