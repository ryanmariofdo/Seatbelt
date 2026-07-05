# Contributing

Thanks for considering a contribution to Seatbelt — PRs are welcome, including small ones (typo fixes, doc improvements, a single new `Configuration`).

## Getting started

```bash
git clone https://github.com/Hashane/Seatbelt.git
cd Seatbelt
composer install
```

## Before opening a PR

Run the full quality gate locally — it's the same thing CI checks:

```bash
composer quality
```

This runs, in order: Rector (dry-run), Pint, PHPStan, and the Pest suite. All four must pass.

## Adding a new Configuration

Each boot-time behavior is a small class implementing `Judehashane\Seatbelt\Contracts\Configuration`:

```php
interface Configuration
{
    public function enabled(): bool;

    public function apply(): void;
}
```

- `enabled()` should read from injected `Application`/`Repository` (constructor-injected, not the `app()`/`config()` global helpers) — this keeps the class testable in isolation with Mockery, without booting a full app.
- Register it in the `configurations` array in `config/seatbelt.php`, and add a config key (or nested group, if it needs more than one setting — see `database` or `password` for examples) with a doc comment explaining what it does and why it's gated the way it is.
- Add a test file covering `enabled()`'s boundary cases and the actual effect of `apply()` — not just that it doesn't throw. If it changes real framework behavior (Eloquent, validation, console commands), test that behavior directly rather than mocking the class you're testing.

## Commit messages

No strict format required — just describe the *why*, not just the *what*.

## Reporting a bug

Open an issue with: the `Configuration` class involved (if any), your Laravel version, and what you expected vs. what happened. A failing test that reproduces it is the fastest path to a fix.
