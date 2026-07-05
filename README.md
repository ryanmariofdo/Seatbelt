# Blueprint

Opinionated, production-ready Laravel standards — all in one go.

Blueprint does two things:

1. **Applies a set of production-safety and dev-quality defaults automatically** the moment it's installed — no config publishing required. Things like blocking `db:wipe` in production, enforcing strong passwords, and catching N+1 queries in development.
2. **Bootstraps a new project's tooling** — Pint, PHPStan/Larastan, and Rector configs — via a single install command.

## Installation

```bash
composer require judehashane/blueprint
```

That's it — the defaults below are active immediately. If you want to see or change them, publish the config:

```bash
php artisan vendor:publish --tag=blueprint-config
```

## What it does

Each behavior below is its own `Configuration` class, listed in the `configurations` array in `config/blueprint.php`. Remove an entry from that array to disable it entirely, or leave it in and use its config key to toggle it. All of them are on by default.

| Configuration | Active when | Config key(s) | What it does |
|---|---|---|---|
| `ProhibitDestructiveCommands` | production | `prohibit_destructive_commands` | Blocks `db:wipe`, `migrate:fresh`, `migrate:reset`, `migrate:refresh`, and `migrate:rollback` — even with `--force`. |
| `DefaultPasswordRules` | production | `password.enforce_rule`, `password.min_length`, `password.max_length`, `password.require_letter`, `password.require_numbers`, `password.require_symbols` | Sets a strong default password validation rule via `Password::defaults()` — minimum/maximum length, optional character-class requirements, and an always-on check against publicly breached passwords (Have I Been Pwned). |
| `StrictModels` | outside production | `enforce_strict_models` | Calls `Model::shouldBeStrict()` — turns lazy-loading violations, silently discarded mass-assignment attributes, and missing-attribute access into exceptions during development, so you catch them before they ship. |
| `ForceHttpsScheme` | production | `force_https_scheme` | Calls `URL::forceScheme('https')` — generated URLs (`route()`, `url()`, `asset()`) always use `https://`, including behind a proxy that terminates TLS before the request reaches Laravel. |
| `DatabaseMonitoring` | outside production | `database.enforce_monitoring`, `database.query_budget_ms` | Logs a warning once a connection's cumulative query time exceeds the configured budget — surfaces slow queries and N+1s while you're building the feature. |
| `AutomaticEagerLoading` | production | `automatically_eager_load_relationships` | Calls `Model::automaticallyEagerLoadRelationships()` — an N+1 that slips past `StrictModels` in development gets silently upgraded to an eager load in production instead of hitting real users with per-row queries. |
| `ViteAggressivePrefetching` | production | `vite_aggressive_prefetching` | Calls `Vite::useAggressivePrefetching()` — prefetches all built JS/CSS chunks in the background after initial load. |
| `PreventStrayRequests` | test suite only | `prevent_stray_requests` | Calls `Http::preventStrayRequests()` — an un-faked `Http::` call throws instead of hitting the real network. |
| `PreventStrayProcesses` | test suite only | `prevent_stray_processes` | Calls `Process::preventStrayProcesses()` — an un-faked `Process::` call throws instead of shelling out for real, once something in the test has also called `Process::fake()`. |

A few of these are deliberately environment-gated in opposite directions on purpose: `StrictModels` and `DatabaseMonitoring` are noisy-by-design developer feedback, so they're off in production to avoid throwing in front of real users. `ProhibitDestructiveCommands`, `ForceHttpsScheme`, and `AutomaticEagerLoading` are production safety nets that would just get in the way locally. `PreventStrayRequests` and `PreventStrayProcesses` are narrower still — gated on the test environment specifically, not just "outside production", since applying them during ordinary local development would break every real outbound request or shell command your app makes.

## Tooling bootstrap

Run the install command to also publish tooling configs into a fresh project:

```bash
php artisan blueprint:install
```

This publishes:

- `config/blueprint.php` — Blueprint's own configuration (same as `vendor:publish --tag=blueprint-config`)
- `pint.json`, `phpstan.neon`, `rector.php` — tooling configs, at your project root

These are config files, not the tools themselves — Blueprint doesn't install Pint/PHPStan/Rector into your app (doing so would ship a static-analysis toolchain into every production deploy for zero runtime benefit). `laravel/pint` already ships with a default Laravel app; for the rest, install what the published configs expect:

```bash
composer require --dev larastan/larastan mrpunyapal/peststan rector/rector driftingly/rector-laravel mrpunyapal/rector-pest
```

The published `phpstan.neon`/`rector.php` are thin — they only wire up project-specific bits (paths, cache directory) and then reference Blueprint's own curated rule set/config live from `vendor/judehashane/blueprint`, via `includes:` and `BlueprintSetList::RECOMMENDED` respectively. That means `composer update judehashane/blueprint` picks up rule changes automatically — no re-running `blueprint:install --force` and no losing any customization you've made to the published files. Only `pint.json` is a full static copy (Pint doesn't have an equivalent "reference a vendor package's ruleset" mechanism), so re-publish with `--force` if Blueprint's Pint rules change.

Pass `--force` to overwrite files that already exist:

```bash
php artisan blueprint:install --force
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
