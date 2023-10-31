<?php

declare(strict_types=1);

namespace KCode\Modulith;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

/**
 * This is the service provider.
 *
 * Place the line below in the providers array inside app/config/app.php
 * <code>'KCode\Modulith\ModulithServiceProvider',</code>
 *
 * @author KalinkoCode
 **/
class ModulithServiceProvider extends ServiceProvider
{
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'KCode\Modulith\Commands\NewPackage',
        'KCode\Modulith\Commands\RemovePackage',
        'KCode\Modulith\Commands\GetPackage',
        'KCode\Modulith\Commands\GitPackage',
        'KCode\Modulith\Commands\ListPackages',
        'KCode\Modulith\Commands\MoveTests',
        'KCode\Modulith\Commands\CheckPackage',
        'KCode\Modulith\Commands\PublishPackage',
        'KCode\Modulith\Commands\EnablePackage',
        'KCode\Modulith\Commands\DisablePackage',
    ];

    /**
     * Bootstrap the application events.
     *
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/modulith.php' => config_path('packager.php'),
        ]);
    }

    /**
     * Register the command.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/modulith.php', 'packager');

        $this->commands($this->commands);
    }
}
