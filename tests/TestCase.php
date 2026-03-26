<?php

namespace Laraditz\Lazada\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laraditz\Lazada\LazadaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [LazadaServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Use SQLite in-memory for all tests
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Package config defaults for tests
        $app['config']->set('lazada.app_key', 'test_app_key');
        $app['config']->set('lazada.app_secret', 'test_app_secret');
        $app['config']->set('lazada.region', 'MY');
        $app['config']->set('lazada.seller_short_code', 'TESTSHOP');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
