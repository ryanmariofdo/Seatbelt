<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Judehashane\Blueprint\Configurations\DefaultPasswordRules;

afterEach(function (): void {
    Password::$defaultCallback = null;
});

function applyPasswordRule(array $overrides = []): void
{
    $settings = array_merge([
        'blueprint.password.min_length' => 8,
        'blueprint.password.max_length' => 20,
        'blueprint.password.require_letter' => false,
        'blueprint.password.require_numbers' => false,
        'blueprint.password.require_symbols' => false,
    ], $overrides);

    $app = Mockery::mock(Application::class);
    $app->shouldReceive('isProduction')->andReturn(true);

    $config = Mockery::mock(Repository::class);

    foreach ($settings as $key => $value) {
        $config->shouldReceive('get')->with($key, Mockery::any())->andReturn($value);
    }

    (new DefaultPasswordRules($app, $config))->apply();
}

it('enforces the configured length boundaries', function (string $password, bool $shouldPass): void {
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
    applyPasswordRule();

    $validator = Validator::make(['password' => $password], ['password' => Password::default()]);

    expect($validator->passes())->toBe($shouldPass);
})->with([
    'too short' => ['short12', false],
    'too long' => [str_repeat('a', 30), false],
    'within range' => ['longenough1', true],
]);

it('enforces character-class requirements when enabled', function (): void {
    Http::fake(['api.pwnedpasswords.com/*' => Http::response('')]);
    applyPasswordRule(['blueprint.password.require_symbols' => true]);

    $withoutSymbol = Validator::make(['password' => 'abcdefgh'], ['password' => Password::default()]);
    $withSymbol = Validator::make(['password' => 'abcdefg!'], ['password' => Password::default()]);

    expect($withoutSymbol->fails())->toBeTrue()
        ->and($withSymbol->passes())->toBeTrue();
});

it('rejects passwords found in a public breach', function (): void {
    // sha1('password') = 5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8 — a known-breached password.
    Http::fake([
        'https://api.pwnedpasswords.com/range/5BAA6' => Http::response('1E4C9B93F3F0682250B6CF8331B7EE68FD8:3861493'),
        'api.pwnedpasswords.com/*' => Http::response(''),
    ]);
    applyPasswordRule();

    $validator = Validator::make(['password' => 'password'], ['password' => Password::default()]);

    expect($validator->fails())->toBeTrue();
});
