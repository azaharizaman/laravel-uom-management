# Unit of Measurement (UOM) Management
Comprehensive Laravel Package for Sophisticated UOM Handling

# Laravel UOM Agent Notes

## Repo Snapshot
- !IMPORTANT! always refer to and update the Project Requirements Document (./PRD.md) for overarching goals and constraints. Do not modify the PRD.md file directly without any PRD_CHANGELOG tracking.
- Laravel package skeleton for unit-of-measure management; runtime code belongs in `src/` under `AzahariZaman\\LaravelUomManagement`.
- All namespace under src/ should reflect `AzahariZaman` with a capital 'Z' as per PSR-4 standards.
- `src/` is currently empty, so any feature work will establish the initial directory layout and service provider.
- `composer.json` supplies the authoritative dependency list; no other docs describe architecture, so treat the repository state as the source of truth.
- No config, migrations, or tests are committed yet—assume greenfield implementations unless files are added in your branch.

## Dependencies & Tooling
- PHP 8.2+ with target Laravel 12 compatibility via `illuminate/*` packages; keep new code framework-agnostic where possible.
- `spatie/laravel-package-tools` (dev dependency) should back the eventual `LaravelUomManagementServiceProvider` registration helpers.
- `orchestra/testbench` is available for package-level feature testing; extend `Orchestra\\Testbench\\TestCase` for integration coverage.
- `brick/math` is already required—prefer its precise number types for conversion math instead of native floats.

## Build & Test Workflow
- Run `composer install` before hacking; use `composer dump-autoload` whenever you add new classes so Testbench locates them.
- With no project-level PHPUnit config, invoke `vendor/bin/phpunit -c vendor/orchestra/testbench-core/phpunit.xml` until a local `phpunit.xml` is introduced.
- Place integration specs in `tests/Feature` and low-level specs in `tests/Unit`; Testbench auto-discovers once the directories exist.
- When adding console tooling, register commands through Package Tools’ `$package->hasCommand()` helpers to keep discovery automatic.

## Implementation Expectations
- Domain centers on unit types, units, conversions, aliases, packaging, and audit logs—model naming should follow that vocabulary.
- Favor Eloquent models with typed properties, attribute casting, and dedicated traits for cross-cutting concerns (precision, metadata, symbols) as those behaviors land.
- Plan to publish `config/uom.php` from the service provider once configurable options solidify; keep defaults idempotent for package consumers.
- Document non-obvious business rules (e.g., single base unit per type, immutable conversion logs) alongside the code or in future README updates.

## Collaboration Notes
- Keep this instruction file aligned with the actual tree; remove or update notes whenever new structures replace placeholders.
- Introduce stub `README.md` files in new directories if structure needs explanation before full documentation is written.
- Update dependency constraints thoughtfully; Laravel 13 support currently points to `13.0.x-dev`, so monitor upstream changes before locking versions.
- Open follow-up issues or PRs to cover migration strategy and seeding once schema work begins—none of that exists in main yet.
