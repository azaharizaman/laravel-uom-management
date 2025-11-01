# Laravel UOM Package Roadmap

Keep this checklist updated as work lands in `main` so we can see the overall progress at a glance.

## 1. Repository Foundations
- [x] Establish Composer package metadata and PSR-4 autoloading (`composer.json`)
- [x] Configure dev autoloading for factories (`composer.json`)
- [ ] Add base README with package overview and installation steps
- [ ] Set up CI workflow (PHPUnit + static analysis) in GitHub Actions

## 2. Data Layer
- [x] Create initial migration covering core UOM tables (`database/migrations/create_uom_tables.php`)
- [x] Scaffold Eloquent models for all domain entities (`src/Models/*`)
- [x] Provide model factories for testing (`database/factories/*`)
- [x] Add database seeders for sample datasets (`database/seeders`)

## 3. Service Provider & Configuration
- [x] Implement `LaravelUomManagementServiceProvider` with model bindings and package registration
- [x] Publishable configuration file (`config/uom.php`) with sensible defaults
- [ ] Bind conversion services and helpers into the container

## 4. Domain Services & Features
- [ ] Implement core conversion service leveraging `brick/math`
- [ ] Add alias resolution and lookup helpers
- [ ] Support compound unit conversion logic
- [ ] Implement packaging resolution utilities (base ↔ package)
- [ ] Provide custom unit registration APIs with validation

## 5. Console & Artisan Tooling
- [ ] Register artisan commands for managing units and conversions
- [ ] Offer seeding/import command for baseline unit sets

## 6. Testing Strategy
- [ ] Establish PHPUnit/Testbench harness (`tests/Feature`, `tests/Unit`)
- [ ] Cover migrations and models with unit tests
- [ ] Add feature tests for conversion flows and packaging logic
- [ ] Track coverage for critical paths (conversion, custom units)

## 7. Documentation & Examples
- [ ] Document configuration options and publishing steps
- [ ] Provide usage examples for conversions, packaging, custom units
- [ ] Add upgrade and contribution guidelines

## 8. Release Preparation
- [ ] Tag initial release (v0.1.0) once core features complete
- [ ] Draft CHANGELOG capturing highlights
- [ ] Prepare Packagist submission notes and marketing copy
