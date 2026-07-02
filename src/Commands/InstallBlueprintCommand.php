<?php

declare(strict_types=1);

namespace Judehashane\Blueprint\Commands;

use Illuminate\Console\Command;
use Judehashane\Blueprint\BlueprintServiceProvider;

final class InstallBlueprintCommand extends Command
{
    protected $signature = 'blueprint:install {--force : Overwrite any existing published files}';

    protected $description = 'Install Blueprint into your Laravel application.';

    public function handle(): int
    {
        $this->info('Installing Blueprint...');

        $this->info('Publishing configuration file...');
        $this->callSilent('vendor:publish', [
            '--provider' => BlueprintServiceProvider::class,
            '--tag' => 'blueprint-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('Publishing stubs...');
        $this->callSilent('vendor:publish', [
            '--provider' => BlueprintServiceProvider::class,
            '--tag' => 'blueprint-stubs',
            '--force' => $this->option('force'),
        ]);

        $this->info('Blueprint installed successfully.');

        return self::SUCCESS;
    }
}
