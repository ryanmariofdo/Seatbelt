<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\StrayRequestException;
use Illuminate\Support\Facades\Http;
use Judehashane\Seatbelt\Configurations\PreventStrayRequests;

afterEach(function (): void {
    Http::preventStrayRequests(false);
});

it('throws when an http call has no matching fake once applied', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new PreventStrayRequests($app, $config))->apply();

    Http::get('https://example.com');
})->throws(StrayRequestException::class);

it('still allows requests that are explicitly faked', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new PreventStrayRequests($app, $config))->apply();

    Http::fake(['https://example.com/*' => Http::response(['ok' => true])]);

    expect(Http::get('https://example.com/ping')->json('ok'))->toBeTrue();
});
