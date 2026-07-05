<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Judehashane\Seatbelt\Configurations\ForceHttpsScheme;

afterEach(function (): void {
    URL::forceScheme(null);
});

it('forces generated urls to https even when the current request is plain http', function (): void {
    $this->app->instance('request', Request::create('http://example.com/'));

    // Sanity check first: without forcing, URL generation follows the actual
    // (insecure) request scheme — proves the simulated request is genuinely http.
    expect(url('/dashboard'))->toStartWith('http://')
        ->and(url('/dashboard'))->not->toStartWith('https://');

    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(true);

    $config = Mockery::mock(Repository::class);
    $config->shouldReceive('get')->with('seatbelt.force_https_scheme', true)->andReturn(true);

    (new ForceHttpsScheme($app, $config))->apply();

    expect(url('/dashboard'))->toStartWith('https://');
});
