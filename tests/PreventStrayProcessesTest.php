<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Process;
use Judehashane\Seatbelt\Configurations\PreventStrayProcesses;

afterEach(function (): void {
    Process::preventStrayProcesses(false);
});

it('throws when a faked process run has no matching fake once applied', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new PreventStrayProcesses($app, $config))->apply();

    Process::fake([]);

    Process::run('echo "unmatched"');
})->throws(RuntimeException::class);

it('does not interfere with real process runs when nothing is faked', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new PreventStrayProcesses($app, $config))->apply();

    expect(Process::run('echo "hello"')->successful())->toBeTrue();
});
