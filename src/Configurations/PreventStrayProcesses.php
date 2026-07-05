<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Configurations;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Process;
use Judehashane\Seatbelt\Contracts\Configuration;

final class PreventStrayProcesses implements Configuration
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function enabled(): bool
    {
        return $this->app->runningUnitTests()
            && $this->config->get('seatbelt.prevent_stray_processes', true);
    }

    public function apply(): void
    {
        Process::preventStrayProcesses();
    }
}
