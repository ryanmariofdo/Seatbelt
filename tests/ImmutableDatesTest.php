<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Date;
use Judehashane\Seatbelt\Configurations\ImmutableDates;

afterEach(function (): void {
    Date::useDefault();
});

it('is enabled when its config flag is on', function (): void {
    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with('seatbelt.immutable_dates', true)->andReturn(true);

    expect((new ImmutableDates($config))->enabled())->toBeTrue();
});

it('is disabled when its config flag is off', function (): void {
    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with('seatbelt.immutable_dates', true)->andReturn(false);

    expect((new ImmutableDates($config))->enabled())->toBeFalse();
});

it('makes dates immutable', function (): void {
    $config = Mockery::mock(Repository::class);

    (new ImmutableDates($config))->apply();

    $originalDateString = now()->toDateString();
    $date = now();
    $tomorrow = $date->addDay();

    expect($tomorrow)->not->toBe($date);
    expect($date->toDateString())->toBe($originalDateString);
});
