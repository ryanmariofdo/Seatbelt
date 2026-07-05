<?php

declare(strict_types=1);

namespace Judehashane\Blueprint\Configurations;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Judehashane\Blueprint\Contracts\Configuration;

final class QueueFailedJobLogging implements Configuration
{
    public function __construct(
        private readonly Application $app,
        private readonly Repository $config,
    ) {}

    public function enabled(): bool
    {
        return $this->app->isProduction()
            && $this->config->get('blueprint.queue_failed_job_logging', true);
    }

    public function apply(): void
    {
        Queue::failing(function (JobFailed $event): void {
            Log::error('Queue job failed.', [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job' => $event->job->resolveName(),
                'exception' => $event->exception,
            ]);
        });
    }
}
