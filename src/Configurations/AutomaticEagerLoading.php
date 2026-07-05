<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Configurations;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Judehashane\Seatbelt\Contracts\Configuration;

final class AutomaticEagerLoading implements Configuration
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function enabled(): bool
    {
        return $this->app->isProduction()
            && $this->config->get('seatbelt.automatically_eager_load_relationships', true);
    }

    public function apply(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }
}
