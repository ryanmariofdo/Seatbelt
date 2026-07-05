<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt\Commands;

use Illuminate\Console\Command;
use Judehashane\Seatbelt\SeatbeltServiceProvider;

final class InstallSeatbeltCommand extends Command
{
    protected $signature = 'seatbelt:install {--force : Overwrite any existing published files}';

    protected $description = 'Install Seatbelt into your Laravel application.';

    public function handle(): int
    {
        $this->info('Installing Seatbelt...');

        $this->info('Publishing configuration file...');
        $this->callSilent('vendor:publish', [
            '--provider' => SeatbeltServiceProvider::class,
            '--tag' => 'seatbelt-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('Publishing stubs...');
        $this->callSilent('vendor:publish', [
            '--provider' => SeatbeltServiceProvider::class,
            '--tag' => 'seatbelt-stubs',
            '--force' => $this->option('force'),
        ]);

        $this->info('Seatbelt installed successfully.');

        $this->newLine();
        $this->comment('The published pint.json/phpstan.neon/rector.php expect these dev tools — install what you don\'t already have:');
        $this->line('  composer require --dev larastan/larastan mrpunyapal/peststan rector/rector driftingly/rector-laravel mrpunyapal/rector-pest');

        return self::SUCCESS;
    }
}
