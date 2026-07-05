<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Judehashane\Seatbelt\Configurations\AutomaticEagerLoading;
use Judehashane\Seatbelt\Configurations\DatabaseMonitoring;
use Judehashane\Seatbelt\Configurations\DefaultPasswordRules;
use Judehashane\Seatbelt\Configurations\ForceHttpsScheme;
use Judehashane\Seatbelt\Configurations\PreventStrayProcesses;
use Judehashane\Seatbelt\Configurations\PreventStrayRequests;
use Judehashane\Seatbelt\Configurations\ProhibitDestructiveCommands;
use Judehashane\Seatbelt\Configurations\QueueFailedJobLogging;
use Judehashane\Seatbelt\Configurations\StrictModels;

dataset('production-gated configurations', [
    'ProhibitDestructiveCommands' => [ProhibitDestructiveCommands::class, 'seatbelt.prohibit_destructive_commands'],
    'DefaultPasswordRules' => [DefaultPasswordRules::class, 'seatbelt.password.enforce_rule'],
    'ForceHttpsScheme' => [ForceHttpsScheme::class, 'seatbelt.force_https_scheme'],
    'AutomaticEagerLoading' => [AutomaticEagerLoading::class, 'seatbelt.automatically_eager_load_relationships'],
    'QueueFailedJobLogging' => [QueueFailedJobLogging::class, 'seatbelt.queue_failed_job_logging'],
]);

dataset('dev-gated configurations', [
    'StrictModels' => [StrictModels::class, 'seatbelt.enforce_strict_models'],
    'DatabaseMonitoring' => [DatabaseMonitoring::class, 'seatbelt.database.enforce_monitoring'],
]);

dataset('test-only-gated configurations', [
    'PreventStrayRequests' => [PreventStrayRequests::class, 'seatbelt.prevent_stray_requests'],
    'PreventStrayProcesses' => [PreventStrayProcesses::class, 'seatbelt.prevent_stray_processes'],
]);

it('is enabled in production when its config flag is on', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(true);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with($key, true)->andReturn(true);

    expect((new $class($app, $config))->enabled())->toBeTrue();
})->with('production-gated configurations');

it('is disabled outside production', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(false);

    $config = Mockery::mock(Repository::class);

    expect((new $class($app, $config))->enabled())->toBeFalse();
})->with('production-gated configurations');

it('is disabled in production when its config flag is off', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(true);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with($key, true)->andReturn(false);

    expect((new $class($app, $config))->enabled())->toBeFalse();
})->with('production-gated configurations');

it('is enabled outside production when its config flag is on', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(false);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with($key, true)->andReturn(true);

    expect((new $class($app, $config))->enabled())->toBeTrue();
})->with('dev-gated configurations');

it('is disabled in production regardless of its config flag', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(true);

    $config = Mockery::mock(Repository::class);

    expect((new $class($app, $config))->enabled())->toBeFalse();
})->with('dev-gated configurations');

it('is disabled outside production when its config flag is off', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(false);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with($key, true)->andReturn(false);

    expect((new $class($app, $config))->enabled())->toBeFalse();
})->with('dev-gated configurations');

it('is enabled during the test suite when its config flag is on', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('runningUnitTests')->andReturn(true);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with($key, true)->andReturn(true);

    expect((new $class($app, $config))->enabled())->toBeTrue();
})->with('test-only-gated configurations');

it('is disabled outside the test suite', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('runningUnitTests')->andReturn(false);

    $config = Mockery::mock(Repository::class);

    expect((new $class($app, $config))->enabled())->toBeFalse();
})->with('test-only-gated configurations');

it('is disabled during the test suite when its config flag is off', function (string $class, string $key): void {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('runningUnitTests')->andReturn(true);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with($key, true)->andReturn(false);

    expect((new $class($app, $config))->enabled())->toBeFalse();
})->with('test-only-gated configurations');
