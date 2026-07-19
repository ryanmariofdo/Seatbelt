<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Judehashane\Seatbelt\Configurations\ViteAggressivePrefetching;

beforeEach(function (): void {
    $this->buildDir = Str::random();
    app()->usePublicPath(__DIR__);
    File::ensureDirectoryExists(public_path($this->buildDir));
    file_put_contents(public_path("{$this->buildDir}/manifest.json"), json_encode([
        'resources/js/app.js' => [
            'src' => 'resources/js/app.js',
            'file' => 'assets/app.versioned.js',
        ],
    ]));
});

afterEach(function (): void {
    Vite::usePrefetchStrategy(null);
    File::deleteDirectory(public_path($this->buildDir));
});

it('adds the aggressive prefetch script once applied', function (): void {
    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new ViteAggressivePrefetching($app, $config))->apply();

    $html = (string) Vite::withEntryPoints(['resources/js/app.js'])->useBuildDirectory($this->buildDir)->toHtml();

    expect($html)
        ->toContain('addEventListener')
        ->toContain('fragment.append(makeLink(asset))');
});

it('does not add a prefetch script when left untouched', function (): void {
    $html = (string) Vite::withEntryPoints(['resources/js/app.js'])->useBuildDirectory($this->buildDir)->toHtml();

    expect($html)
        ->not->toContain('addEventListener')
        ->not->toContain('fragment.append(makeLink(asset))');
});
