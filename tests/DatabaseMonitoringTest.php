<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Judehashane\Seatbelt\Configurations\DatabaseMonitoring;

it('logs a warning once the cumulative query time exceeds the configured budget', function (): void {
    Log::spy();

    $app = Mockery::mock(Application::class);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with('seatbelt.database.query_budget_ms', 500)->andReturn(500);

    (new DatabaseMonitoring($app, $config))->apply();

    $connection = DB::connection();
    $connection->resetTotalQueryDuration();
    $connection->allowQueryDurationHandlersToRunAgain();

    $connection->logQuery('select * from large_table', [], 501);

    Log::shouldHaveReceived('warning')
        ->once()
        ->with('Database query budget exceeded.', [
            'connection' => $connection->getName(),
            'total_duration_ms' => 501.0,
            'last_query_duration_ms' => 501,
            'threshold_ms' => 500,
        ]);
});

it('does not log when the query stays within the configured budget', function (): void {
    Log::spy();

    $app = Mockery::mock(Application::class);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with('seatbelt.database.query_budget_ms', 500)->andReturn(500);

    (new DatabaseMonitoring($app, $config))->apply();

    $connection = DB::connection();
    $connection->resetTotalQueryDuration();
    $connection->allowQueryDurationHandlersToRunAgain();

    $connection->logQuery('select * from small_table', [], 100);

    Log::shouldNotHaveReceived('warning');
});
