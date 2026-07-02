# Blueprint

Opinionated, production-ready Laravel standards — all in one go.

Blueprint bootstraps a new Laravel application with a shared set of tooling
configs (Pint, PHPStan/Larastan, Rector, a CI workflow).

## Installation

```bash
composer require judehashane/blueprint
```

Then run the install command:

```bash
php artisan blueprint:install
```

This publishes:

- `config/blueprint.php` — Blueprint's own configuration
- `pint.json`, `phpstan.neon`, `rector.php` — tooling configs, at your project root
- `.github/workflows/tests.yml` — a CI workflow running Pint, PHPStan, and Pest

Pass `--force` to overwrite files that already exist:

```bash
php artisan blueprint:install --force
```

## Testing

```bash
composer test
```

## License

MIT.
