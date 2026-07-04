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

        $this->newLine();
        $this->comment('The published pint.json/phpstan.neon/rector.php expect these dev tools — install what you don\'t already have:');
        $this->line('  composer require --dev larastan/larastan mrpunyapal/peststan rector/rector driftingly/rector-laravel mrpunyapal/rector-pest');

        return self::SUCCESS;
    }
}
