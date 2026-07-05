<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Configurations;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use Judehashane\Seatbelt\Contracts\Configuration;

final class ForceHttpsScheme implements Configuration
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function enabled(): bool
    {
        return $this->app->isProduction()
            && $this->config->get('seatbelt.force_https_scheme', true);
    }

    public function apply(): void
    {
        URL::forceScheme('https');
    }
}
