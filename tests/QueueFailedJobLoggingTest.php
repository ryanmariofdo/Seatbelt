<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Judehashane\Seatbelt\Configurations\QueueFailedJobLogging;

it('logs a failed job with connection, queue, job, and exception context', function (): void {
    Log::spy();

    $app = Mockery::mock(Application::class);
    $config = Mockery::mock(Repository::class);

    (new QueueFailedJobLogging($app, $config))->apply();

    $job = Mockery::mock(Job::class);
    $job->shouldReceive('getQueue')->andReturn('emails');
    $job->shouldReceive('resolveName')->andReturn('SendEmailJob');

    $exception = new Exception('Something broke');

    event(new JobFailed('test', $job, $exception));

    Log::shouldHaveReceived('error')
        ->once()
        ->with('Queue job failed.', [
            'connection' => 'test',
            'queue' => 'emails',
            'job' => 'SendEmailJob',
            'exception' => $exception,
        ]);
});
