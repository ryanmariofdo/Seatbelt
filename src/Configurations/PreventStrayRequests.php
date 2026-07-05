<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Configurations;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Judehashane\Seatbelt\Contracts\Configuration;

final class PreventStrayRequests implements Configuration
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function enabled(): bool
    {
        return $this->app->runningUnitTests()
            && $this->config->get('seatbelt.prevent_stray_requests', true);
    }

    public function apply(): void
    {
        Http::preventStrayRequests();
    }
}
