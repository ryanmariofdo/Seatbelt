<?php

declare(strict_types=1);

namespace Judehashane\Seatbelt;

use Illuminate\Support\ServiceProvider;
use Judehashane\Seatbelt\Commands\InstallSeatbeltCommand;
use Judehashane\Seatbelt\Contracts\Configuration;

final class SeatbeltServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/seatbelt.php', 'seatbelt');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/seatbelt.php' => config_path('seatbelt.php'),
        ], 'seatbelt-config');

        $this->publishes([
            __DIR__.'/../stubs/pint.json' => base_path('pint.json'),
            __DIR__.'/../stubs/phpstan.neon' => base_path('phpstan.neon'),
            __DIR__.'/../stubs/rector.php' => base_path('rector.php'),
        ], 'seatbelt-stubs');

        $this->applyConfigurations();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallSeatbeltCommand::class,
            ]);
        }
    }

    private function applyConfigurations(): void
    {
        /** @var array<int, class-string<Configuration>> $configurations */
        $configurations = config('seatbelt.configurations', []);

        collect($configurations)
            ->map(fn (string $configuration): Configuration => $this->app->make($configuration))
            ->filter(fn (Configuration $configuration): bool => $configuration->enabled())
            ->each(fn (Configuration $configuration) => $configuration->apply());
    }
}
