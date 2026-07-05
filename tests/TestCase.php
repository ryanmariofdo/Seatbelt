<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Tests;

use Judehashane\Seatbelt\SeatbeltServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SeatbeltServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Each Configuration is instantiated and applied explicitly within its own
        // test. Without this, SeatbeltServiceProvider::boot() would auto-apply every
        // dev-gated configuration (StrictModels, DatabaseMonitoring) to every test in
        // the suite, since the testing environment satisfies `! isProduction()`.
        $app['config']->set('seatbelt.configurations', []);
    }
}
