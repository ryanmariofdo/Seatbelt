<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Judehashane\Seatbelt\Configurations\ProhibitDestructiveCommands;

afterEach(function (): void {
    DB::prohibitDestructiveCommands(false);
});

it('blocks db:wipe once applied', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new ProhibitDestructiveCommands($app, $config))->apply();

    $this->artisan('db:wipe')
        ->expectsOutputToContain('This command is prohibited from running in this environment.')
        ->assertExitCode(1);
});
