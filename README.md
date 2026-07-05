# Seatbelt

[![Tests](https://github.com/Hashane/Seatbelt/actions/workflows/tests.yml/badge.svg)](https://github.com/Hashane/Seatbelt/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.3-777BB4?logo=php&logoColor=white)](composer.json)
[![Laravel Version](https://img.shields.io/badge/Laravel-%5E12%20%7C%20%5E13-FF2D20?logo=laravel&logoColor=white)](composer.json)
![Buckle Up](.github/badges/buckle-up.svg)

[![Tests](https://github.com/Hashane/Blueprint/actions/workflows/tests.yml/badge.svg)](https://github.com/Hashane/Blueprint/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

Opinionated, production-ready Laravel standards — all in one go.

It applies a set of production-safety and dev-quality defaults the moment it's installed — no config publishing required, no provider to wire up by hand. It also bootstraps a fresh project's tooling — Pint, PHPStan/Larastan, and Rector — via a single install command.

## Installation

```bash
composer require judehashane/seatbelt
```

That's it — the defaults below are active immediately. If you want to see or change them, publish the config:

```bash
php artisan vendor:publish --tag=seatbelt-config
```

## What it does

Each behavior below is its own `Configuration` class, listed in the `configurations` array in `config/seatbelt.php`. Remove an entry from that array to disable it entirely, or leave it in and use its config key to toggle it. All of them are on by default.

| Configuration                 | Active when        | Config key(s)                                                                                                                                            | What it does                                                                                                                                                                                                               |
| ----------------------------- | ------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `ProhibitDestructiveCommands` | production         | `prohibit_destructive_commands`                                                                                                                          | Blocks `db:wipe`, `migrate:fresh`, `migrate:reset`, `migrate:refresh`, and `migrate:rollback` — even with `--force`.                                                                                                       |
| `DefaultPasswordRules`        | production         | `password.enforce_rule`, `password.min_length`, `password.max_length`, `password.require_letter`, `password.require_numbers`, `password.require_symbols` | Sets a strong default password validation rule via `Password::defaults()` — minimum/maximum length, optional character-class requirements, and an always-on check against publicly breached passwords (Have I Been Pwned). |
| `StrictModels`                | outside production | `enforce_strict_models`                                                                                                                                  | Calls `Model::shouldBeStrict()` — turns lazy-loading violations, silently discarded mass-assignment attributes, and missing-attribute access into exceptions during development, so you catch them before they ship.       |
| `ForceHttpsScheme`            | production         | `force_https_scheme`                                                                                                                                     | Calls `URL::forceScheme('https')` — generated URLs (`route()`, `url()`, `asset()`) always use `https://`, including behind a proxy that terminates TLS before the request reaches Laravel.                                 |
| `DatabaseMonitoring`          | outside production | `database.enforce_monitoring`, `database.query_budget_ms`                                                                                                | Logs a warning once a connection's cumulative query time exceeds the configured budget — surfaces slow queries and N+1s while you're building the feature.                                                                 |
| `AutomaticEagerLoading`       | production         | `automatically_eager_load_relationships`                                                                                                                 | Calls `Model::automaticallyEagerLoadRelationships()` — an N+1 that slips past `StrictModels` in development gets silently upgraded to an eager load in production instead of hitting real users with per-row queries.      |
| `ViteAggressivePrefetching`   | production         | `vite_aggressive_prefetching`                                                                                                                            | Calls `Vite::useAggressivePrefetching()` — prefetches all built JS/CSS chunks in the background after initial load.                                                                                                        |
| `PreventStrayRequests`        | test suite only    | `prevent_stray_requests`                                                                                                                                 | Calls `Http::preventStrayRequests()` — an un-faked `Http::` call throws instead of hitting the real network.                                                                                                               |
| `PreventStrayProcesses`       | test suite only    | `prevent_stray_processes`                                                                                                                                | Calls `Process::preventStrayProcesses()` — an un-faked `Process::` call throws instead of shelling out for real, once something in the test has also called `Process::fake()`.                                             |
| `QueueFailedJobLogging`       | production         | `queue_failed_job_logging`                                                                                                                               | Calls `Queue::failing()` — logs the connection, queue, job class, and exception whenever a queued job exhausts its retries and fails.                                                                                      |
| `ImmutableDates`              | always             | `immutable_dates`                                                                                                                                        | Calls `Date::use(CarbonImmutable::class)` — every environment gets immutable `Carbon` instances, so code relying on mutable in-place mutation fails the same way in development as it would in production.                 |

A few of these are gated in opposite directions on purpose. `StrictModels` and `DatabaseMonitoring` are noisy by design, so they're off in production. `ProhibitDestructiveCommands`, `ForceHttpsScheme`, and `AutomaticEagerLoading` run the other way — production-only safety nets that would just get in the way locally. `PreventStrayRequests` and `PreventStrayProcesses` are narrower still, tied to the test environment specifically, since enabling them locally would break every real request or shell command your app makes. `ImmutableDates` isn't gated at all — a mutability bug should show up the same way everywhere.

## Tooling bootstrap

Run the install command to also publish tooling configs into a fresh project:

```bash
php artisan seatbelt:install
```

This publishes `config/seatbelt.php` plus `pint.json`, `phpstan.neon`, and `rector.php` at your project root.

These are config files, not the tools — install what they expect (`laravel/pint` already ships with a default Laravel app):

```bash
composer require --dev larastan/larastan mrpunyapal/peststan rector/rector driftingly/rector-laravel mrpunyapal/rector-pest
```

`phpstan.neon` and `rector.php` stay thin on purpose — they reference Seatbelt's curated rules live from `vendor/judehashane/seatbelt`, so `composer update judehashane/seatbelt` picks up rule changes without re-publishing anything. `pint.json` is the exception: a full static copy, since Pint has no equivalent way to reference a vendor package's ruleset. Re-publish it with `--force` if Seatbelt's Pint rules change.

Pass `--force` to overwrite files that already exist:

```bash
php artisan seatbelt:install --force
```

## Testing

```bash
composer test
```

Or run the full quality gate (Rector dry-run, Pint, PHPStan, Pest):

```bash
composer quality
```

## Contributing

PRs are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md) for setup and what's expected before opening one.

## License

MIT.
