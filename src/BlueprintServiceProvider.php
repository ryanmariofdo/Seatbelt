<?php

declare(strict_types=1);

namespace Judehashane\Blueprint;

use Illuminate\Support\ServiceProvider;
use Judehashane\Blueprint\Commands\InstallBlueprintCommand;

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
            __DIR__.'/../stubs/tests.yml' => base_path('.github/workflows/tests.yml'),
        ], 'blueprint-stubs');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallBlueprintCommand::class,
            ]);
        }
    }
}
