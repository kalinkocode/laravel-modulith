<?php

declare(strict_types=1);

namespace KCode\Modulith\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use KCode\Modulith\ModulithServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'KCode\\LaravelModulith\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ModulithServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-modulith_table.php.stub';
        $migration->up();
        */
    }
}
