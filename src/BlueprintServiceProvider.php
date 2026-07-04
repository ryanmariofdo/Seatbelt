<?php

declare(strict_types=1);

namespace Judehashane\Blueprint;

use Illuminate\Support\ServiceProvider;
use Judehashane\Blueprint\Commands\InstallBlueprintCommand;
use Judehashane\Blueprint\Contracts\Configuration;

final class BlueprintServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/blueprint.php', 'blueprint');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/blueprint.php' => config_path('blueprint.php'),
        ], 'blueprint-config');

        $this->publishes([
            __DIR__.'/../stubs/pint.json' => base_path('pint.json'),
            __DIR__.'/../stubs/phpstan.neon' => base_path('phpstan.neon'),
            __DIR__.'/../stubs/rector.php' => base_path('rector.php'),
        ], 'blueprint-stubs');

        $this->applyConfigurations();

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallBlueprintCommand::class,
            ]);
        }
    }

    private function applyConfigurations(): void
    {
        /** @var array<int, class-string<Configuration>> $configurations */
        $configurations = config('blueprint.configurations', []);

        collect($configurations)
            ->map(fn (string $configuration): Configuration => $this->app->make($configuration))
            ->filter(fn (Configuration $configuration): bool => $configuration->enabled())
            ->each(fn (Configuration $configuration) => $configuration->apply());
    }
}
