<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Configurations;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Judehashane\Seatbelt\Contracts\Configuration;

final class DatabaseMonitoring implements Configuration
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function enabled(): bool
    {
        return ! $this->app->isProduction()
            && $this->config->get('seatbelt.database.enforce_monitoring', true);
    }

    public function apply(): void
    {
        $configuredThreshold = $this->config->get('seatbelt.database.query_budget_ms', 500);

        $threshold = is_int($configuredThreshold) ? $configuredThreshold : 500;

        DB::whenQueryingForLongerThan(
            $threshold,
            function (
                Connection $connection,
                QueryExecuted $query,
            ) use ($threshold): void {
                Log::warning('Database query budget exceeded.', [
                    'connection' => $connection->getName(),
                    'total_duration_ms' => $connection->totalQueryDuration(),
                    'last_query_duration_ms' => $query->time,
                    'threshold_ms' => $threshold,
                ]);
            },
        );
    }
}
