
---

## Sample Folder Structure (with sample files)
- Use spatie/laravel-package-tools to scaffold the package structure.
- Use orchestra/testbench for package testing.

```
database/
├── migrations/
│   └── 2024_01_01_000000_create_uom_tables.php
├── factories/
│   └── UOMUnitFactory.php
├── seeders/
│   └── UOMSeeder.php
src/
|---Casts/
│   └── UnitCast.php
│---Commands/
│   └── ConvertUnitCommand.php
├── Models/
│   └── Unit.php
├── Services/
│   └── UnitConverter.php
├── Enums/
│   └── UnitType.php
├── Exceptions/
│   └── IncompatibleUnitsException.php
├── Facades/
│   └── UOM.php
├── Helpers/
│   └── format_unit.php
├── Traits/
│   └── HasUnits.php
├── UomManagementServiceProvider.php
config/
└── uom.php
tests/
├── features/
│   └── UnitConversionTest.php
└── unit/
    ├── UnitConversionTest.php
    └── UOMPackageTest.php
docs/
└── UOM_Usage_Guide.md

```

## 🧱 Core Entities

### 1. `uom_types`
Defines categories of measurement.

| Column         | Type        | Description                       |
|----------------|-------------|-----------------------------------|
| `id`           | integer     | Primary key                       |
| `name`         | string      | Type name (e.g., mass, length)    |
| `description`  | text        | Optional description              |
| `slug`         | string      | Optional unique identifier        |
| `created_at`   | timestamp   |                                   |
| `updated_at`   | timestamp   |                                   |

---

### 2. `uom_units`
Stores individual units.

| Column              | Type        | Description                                |
|---------------------|-------------|--------------------------------------------|
| `id`                | integer     | Primary key                                |
| `code`              | string      | Unique code (e.g., KG, M, L)               |
| `name`              | string      | Unit name (e.g., kilogram)                 |
| `symbol`            | string      | Short symbol (e.g., kg)                    |
| `uom_type_id`       | foreign key | Belongs to `uom_types`                     |
| `conversion_factor` | decimal     | To base unit of its type                   |
| `precision`         | integer     | Decimal places to round to                 |
| `is_base`           | boolean     | Whether it's the base unit of its type     |
| `is_active`         | boolean     | Whether the unit is active                 |
| `metadata`          | json        | Optional extensible data                   |
| `created_at`        | timestamp   |                                            |
| `updated_at`        | timestamp   |                                            |

---

### 3. `uom_conversions`
Defines conversion rules between units.

| Column           | Type        | Description                          |
|------------------|-------------|--------------------------------------|
| `id`             | integer     | Primary key                          |
| `source_unit_id` | foreign key | From `uom_units`                     |
| `target_unit_id` | foreign key | To `uom_units`                       |
| `factor`         | decimal     | Multiplicative conversion factor      |
| `offset`         | decimal     | Additive offset for conversion        |
| `direction`      | enum        | 'both', 'to_target', 'from_target'   |
| `formula`        | string      | Optional expression or callback name |
| `is_linear`      | boolean     | Whether conversion is linear         |
| `created_at`     | timestamp   |                                      |
| `updated_at`     | timestamp   |                                      |

---

### 4. `uom_aliases`
Maps alternate names/symbols.

| Column     | Type        | Description              |
|------------|-------------|--------------------------|
| `id`       | integer     | Primary key              |
| `unit_id`  | foreign key | References `uom_units`   |
| `alias`    | string      | Alternate name or symbol |
| `is_preferred`| boolean     | Preferred alias          |
| `created_at` | timestamp |                          |
| `updated_at` | timestamp |                          |

---

## 📐 Compound & Grouping Entities

### 5. `uom_compound_units`
Defines compound units.

| Column        | Type        | Description                        |
|---------------|-------------|------------------------------------|
| `id`          | integer     | Primary key                        |
| `name`        | string      | Compound unit name                 |
| `symbol`      | string      | Symbol (e.g., kg/m²)               |
| `uom_type_id` | foreign key | Category of compound unit          |
| `created_at`  | timestamp   |                                    |
| `updated_at`  | timestamp   |                                    |

---

### 6. `uom_compound_components`
Maps base units to compound units.

| Column             | Type        | Description                          |
|--------------------|-------------|--------------------------------------|
| `id`               | integer     | Primary key                          |
| `compound_unit_id` | foreign key | References `uom_compound_units`      |
| `unit_id`          | foreign key | References `uom_units`               |
| `exponent`         | integer     | Power of the unit (e.g., -2 for m²)  |
| `created_at`       | timestamp   |                                      |
| `updated_at`       | timestamp   |                                      |

---

### 7. `uom_unit_groups`
Groups units into systems.

| Column       | Type        | Description                  |
|--------------|-------------|------------------------------|
| `id`         | integer     | Primary key                  |
| `name`       | string      | Group name (e.g., metric)    |
| `description`| text        | Optional description         |
| `created_at` | timestamp   |                              |
| `updated_at` | timestamp   |                              |

---

### 8. `uom_unit_group_unit` (pivot)
Links units to groups.

| Column          | Type        | Description                  |
|-----------------|-------------|------------------------------|
| `unit_group_id` | foreign key | References `uom_unit_groups` |
| `unit_id`       | foreign key | References `uom_units`       |

---

## 📦 Packaging Entities

### 9. `uom_packagings`
Defines packaging relationships.

| Column           | Type        | Description                          |
|------------------|-------------|--------------------------------------|
| `id`             | integer     | Primary key                          |
| `base_unit_id`   | foreign key | Inner unit (e.g., bottle)            |
| `package_unit_id`| foreign key | Outer unit (e.g., box)               |
| `quantity`       | integer     | How many base units per package      |
| `label`          | string      | Optional label (e.g., "12-pack")     |
| `metadata`      | json        | Optional extensible data             |
| `created_at`     | timestamp   |                                      |
| `updated_at`     | timestamp   |                                      |

---

### 10. `uom_items`
Real-world items with default UOM. Expected to be overridden by user own models.

| Column           | Type        | Description                          |
|------------------|-------------|--------------------------------------|
| `id`             | integer     | Primary key                          |
| `name`           | string      | Item name (e.g., water bottle)       |
| `default_unit_id`| foreign key | References `uom_units`               |
| `created_at`     | timestamp   |                                      |
| `updated_at`     | timestamp   |                                      |

---

### 11. `uom_item_packagings`
Maps items to packaging options.

| Column        | Type        | Description                          |
|---------------|-------------|--------------------------------------|
| `id`          | integer     | Primary key                          |
| `item_id`     | foreign key | References `uom_items`               |
| `packaging_id`| foreign key | References `uom_packagings`          |
| `created_at`  | timestamp   |                                      |
| `updated_at`  | timestamp   |                                      |

---

## 🔐 Audit & Customization Entities

### 12. `uom_conversion_logs`
Tracks conversions.

| Column           | Type        | Description                          |
|------------------|-------------|--------------------------------------|
| `id`             | integer     | Primary key                          |
| `source_unit_id` | foreign key | From `uom_units`                     |
| `target_unit_id` | foreign key | To `uom_units`                       |
| `factor_used`   | decimal     | Factor applied at that time           |
| `value`          | decimal     | Input value                          |
| `result`         | decimal     | Converted result                     |
| `user_id`        | foreign key | Optional user reference              |
| `timestamp`      | timestamp   | When conversion occurred             |

Note: implement polymorphic relation if needed.

---

### 13. `uom_custom_units`
User-defined units. Allows multi-user support and extension beyond predefined units.

| Column              | Type        | Description                          |
|---------------------|-------------|--------------------------------------|
| `id`                | integer     | Primary key                          |
| `user_id`           | foreign key | Owner of the custom unit             |
| `code`              | string      | Unique code (e.g., KG, M, L)        |
| `name`              | string      | Unit name                            |
| `symbol`            | string      | Symbol                               |
| `description`       | text        | Optional description                 |
| `uom_type_id`       | foreign key | Optional category                    |
| `conversion_factor` | decimal     | To base unit                         |
| `created_at`        | timestamp   |                                      |
| `updated_at`        | timestamp   |                                      |

---

### 14. `uom_custom_conversions`
Custom conversion rules.

| Column                  | Type        | Description                          |
|-------------------------|-------------|--------------------------------------|
| `id`                    | integer     | Primary key                          |
| `source_custom_unit_id`| foreign key | From `uom_custom_units`              |
| `target_custom_unit_id`| foreign key | To `uom_custom_units`                |
| `formula`              | string      | Optional conversion logic            |
| `created_at`           | timestamp   |                                      |
| `updated_at`           | timestamp   |                                      |

---

## Relationship Diagram

[UOMType]
  └── hasMany → [UOMUnit]
  └── hasMany → [UOMCompoundUnit]
  └── hasMany → [UOMCustomUnit]

[UOMUnit]
  └── belongsTo → [UOMType]
  └── hasMany → [UOMConversion] (as source_unit)
  └── hasMany → [UOMConversion] (as target_unit)
  └── hasMany → [UOMAlias]
  └── belongsToMany → [UOMUnitGroup] via [UOMUnitGroupUnit]
  └── hasMany → [UOMCompoundComponent]
  └── hasMany → [UOMPackaging] (as base_unit)
  └── hasMany → [UOMPackaging] (as package_unit)
  └── hasMany → [UOMConversionLog] (as source)
  └── hasMany → [UOMConversionLog] (as target)

[UOMConversion]
  └── belongsTo → [UOMUnit] (source_unit)
  └── belongsTo → [UOMUnit] (target_unit)

[UOMAlias]
  └── belongsTo → [UOMUnit]

[UOMUnitGroup]
  └── belongsToMany → [UOMUnit] via [UOMUnitGroupUnit]

[UOMUnitGroupUnit] (pivot)
  └── belongsTo → [UOMUnit]
  └── belongsTo → [UOMUnitGroup]

[UOMCompoundUnit]
  └── belongsTo → [UOMType]
  └── hasMany → [UOMCompoundComponent]

[UOMCompoundComponent]
  └── belongsTo → [UOMCompoundUnit]
  └── belongsTo → [UOMUnit]

[UOMPackaging]
  └── belongsTo → [UOMUnit] (base_unit)
  └── belongsTo → [UOMUnit] (package_unit)
  └── hasMany → [UOMItemPackaging]

[UOMItem]
  └── belongsTo → [UOMUnit] (default_unit)
  └── hasMany → [UOMItemPackaging]

[UOMItemPackaging]
  └── belongsTo → [UOMItem]
  └── belongsTo → [UOMPackaging]

[UOMConversionLog]
  └── belongsTo → [UOMUnit] (source)
  └── belongsTo → [UOMUnit] (target)
  └── optional belongsTo → [User]

[UOMCustomUnit]
  └── belongsTo → [User]
  └── optional belongsTo → [UOMType]
  └── hasMany → [UOMCustomConversion] (as source)
  └── hasMany → [UOMCustomConversion] (as target)

[UOMCustomConversion]
  └── belongsTo → [UOMCustomUnit] (source)
  └── belongsTo → [UOMCustomUnit] (target)

---
## Model Factories

Absolutely, Azahari — based on your entity relationships, here’s a complete list of **Laravel model factories** you should scaffold to support testing, seeding, and developer experience for your UOM package.

---

## 🏭 Recommended Model Factories

| Factory | Purpose |
|---------|---------|
| `UOMTypeFactory` | Creates unit categories like mass, length, volume |
| `UOMUnitFactory` | Creates individual units (e.g., kg, m, liter) with conversion factors |
| `UOMConversionFactory` | Defines conversion rules between units |
| `UOMAliasFactory` | Maps alternate names/symbols to canonical units |
| `UOMUnitGroupFactory` | Creates unit systems (e.g., metric, imperial) |
| `UOMUnitGroupUnitFactory` | Pivot factory to link units to groups |
| `UOMCompoundUnitFactory` | Creates compound units like `kg/m²` |
| `UOMCompoundComponentFactory` | Maps base units to compound units with exponents |
| `UOMPackagingFactory` | Defines packaging relationships (e.g., 1 box = 12 bottles) |
| `UOMItemFactory` | Creates real-world items with default UOM |
| `UOMItemPackagingFactory` | Maps items to packaging options |
| `UOMConversionLogFactory` | Logs conversion events for auditability |
| `UOMCustomUnitFactory` | Creates user-defined units |
| `UOMCustomConversionFactory` | Defines custom conversion rules between user-defined units |

---

## 🧪 Bonus: Factory Relationships

You’ll want to define relationships inside your factories to support nested creation:

```php
// UOMUnitFactory
public function definition()
{
    return [
        'name' => $this->faker->word,
        'symbol' => $this->faker->lexify('??'),
        'uom_type_id' => UOMType::factory(),
        'conversion_factor' => $this->faker->randomFloat(4, 0.001, 1000),
        'precision' => 2,
        'is_base' => false,
    ];
}
```

---

## 🧰 Suggested Usage

- Use factories in **Orchestra Testbench** to seed test data.
- Use them in **custom seeders** to populate demo units and packaging.
- Expose them via a `SeederService` for users to bootstrap their own UOM sets.

---

## Traits & Helpers

Absolutely, Azahari — here’s a comprehensive list of **model traits** you should consider implementing across your Laravel UOM package. These traits will improve **reusability**, **auditability**, **extensibility**, and **developer experience**, especially for open-source contributors and test-driven workflows.

---

## 🧩 Core Traits for All Models

| Trait | Purpose |
|-------|--------|
| `HasFactory` | Enables Laravel model factories for testing and seeding. |
| `HasSlug` | Automatically generates slugs from names (useful for `UOMType`, `UOMUnit`, `UOMCompoundUnit`). |
| `HasMetadata` | Stores flexible JSON metadata for extensibility. |
| `HasPrecision` | Centralizes rounding logic for conversion results. |
| `HasSymbol` | Standardizes symbol formatting and lookup. |

---

## 🔐 Audit & Traceability Traits

| Trait | Purpose |
|-------|--------|
| `LogsConversions` | Automatically logs conversion events to `UOMConversionLog`. |
| `Auditable` | Tracks created/updated by user (optional if multi-tenant). |
| `ImmutableLoggable` | Ensures conversion logs and audit trails are immutable once written. |

---

## 📦 Packaging & Hierarchy Traits

| Trait | Purpose |
|-------|--------|
| `Packagable` | Adds methods like `->toPackage()` and `->fromPackage()` for nested packaging logic. |
| `HasPackagingHierarchy` | Resolves multi-level packaging (e.g., pallet → box → bottle). |
| `BelongsToPackaging` | Used in `UOMItemPackaging` to resolve packaging relationships. |

---

## 🧠 Conversion Traits

| Trait | Purpose |
|-------|--------|
| `Convertible` | Adds `convertTo()` and `convertFrom()` methods to `UOMUnit`. |
| `HasConversionFactor` | Centralizes logic for linear conversions. |
| `SupportsCustomConversion` | Resolves user-defined formulas from `UOMCustomConversion`. |
| `CompoundConvertible` | Handles compound unit conversion logic (e.g., `kg/m²` to `g/cm²`). |

---

## 🧪 Testing & Developer Traits

| Trait | Purpose |
|-------|--------|
| `TestableConversion` | Adds assertions and helpers for conversion testing. |
| `SeedableUnitSet` | Provides static methods to seed common units (e.g., metric mass, imperial length). |
| `HasAliases` | Resolves alternate names/symbols for lookup and conversion. |

---

## 🧰 Optional Traits for Extensibility

| Trait | Purpose |
|-------|--------|
| `BelongsToUser` | Used in `UOMCustomUnit` and `UOMCustomConversion` for multi-user support. |
| `BelongsToType` | Shared trait for models that relate to `UOMType`. |
| `BelongsToUnit` | Shared trait for models that relate to `UOMUnit` (e.g., alias, conversion, packaging). |

---
## Suggested Observers
Absolutely, Azahari — here’s a comprehensive list of **model observers** you should consider implementing across your Laravel UOM package. These observers will help maintain **data integrity**, **auditability**, and **business logic enforcement**, especially in an open-source context where contributors may not be familiar with all the nuances of unit management.

## 🛡️ Core Observers
| Observer | Purpose |
|----------|---------|
| `UOMUnitObserver` | Listens for changes to UOM models and triggers necessary updates, clean up aliases, enforce base unit rules. |
| `UOMConversionObserver` | Monitors conversion events and logs them for audit purposes. |
| `UOMPackagingObserver` | Ensures packaging relationships remain consistent when units are updated or deleted. |
| `UOMCustomUnitObserver` | Validates user-defined units and ensures they do not conflict with existing units. |

## 🧪 Tests Suggestions
- Create feature tests for each public method in your models.
- Use the `RefreshDatabase` trait to reset the database state between tests.
- Leverage Laravel's built-in testing helpers for assertions.
- Do not use doc-comment for test attribution eg. `@test` - prefer #[Test] attribute or method names starting with `test`.
- For code coverage, user #[CoversClass(ClassName::class)] attribute on test classes to specify which classes are being tested.
- Use model factories to generate test data.

Example functional test cases:

---

### 🔁 Conversion Logic Test

| Test | Purpose |
|------|--------|
| `test_linear_conversion_between_units` | Verifies conversion using `conversion_factor` between units of the same type. |
| `test_custom_formula_conversion` | Validates conversion using a user-defined formula. |
| `test_conversion_to_self_returns_same_value` | Ensures converting a unit to itself returns the original value. |
| `test_conversion_precision_is_applied` | Confirms rounding behavior matches unit precision. |
| `test_conversion_fails_without_path` | Asserts exception is thrown when no conversion path exists. |

---

### 📦 Packaging Logic Test

| Test | Purpose |
|------|--------|
| `test_packaging_quantity_resolution` | Verifies correct quantity between base and package units. |
| `test_convert_package_to_base_units` | Converts package count to total base units. |
| `test_packaging_relationship_exists` | Checks if a unit is packaged in another. |
| `test_circular_packaging_is_prevented` | Ensures packaging relationships don’t loop back. |

---

### 🧮 Compound Units Test

| Test | Purpose |
|------|--------|
| `test_compound_unit_normalization` | Normalizes compound units using component exponents. |
| `test_compound_conversion_between_units` | Converts between compound units like `kg/m²` to `g/cm²`. |

---

### 🧾 Audit & Logging Test

| Test | Purpose |
|------|--------|
| `test_conversion_log_is_created` | Ensures conversion events are logged. |
| `test_conversion_log_is_immutable` | Verifies logs cannot be edited after creation. |
| `test_conversion_log_links_to_user` | Confirms user attribution in logs (if applicable). |

---

### 🧪 Aliases & Lookup Test

| Test | Purpose |
|------|--------|
| `test_unit_can_be_found_by_alias` | Resolves unit by alternate name or symbol. |
| `test_alias_uniqueness_per_unit` | Prevents duplicate aliases for the same unit. |

---

### 🧩 Grouping & Hierarchies Test

| Test | Purpose |
|------|--------|
| `test_unit_group_contains_units` | Verifies units are correctly grouped. |
| `test_unit_group_lookup_resolves_members` | Finds all units in a group. |

---

### 🧰 Custom Units & Conversions Test

| Test | Purpose |
|------|--------|
| `test_custom_unit_belongs_to_user` | Ensures user ownership of custom units. |
| `test_custom_conversion_applies_formula` | Validates user-defined conversion logic. |

---

### 🧪 Seeder & Factory Integration

| Test | Purpose |
|------|--------|
| `test_seeded_units_are_convertible` | Ensures seeded units can convert to each other. |
| `test_packaging_seed_is_valid` | Verifies packaging relationships in seed data. |

---

# Config Options

Great question, Azahari — here’s a curated list of **configuration options** you can expose in your Laravel UOM package to balance flexibility, auditability, and developer control.

---

## ⚙️ Suggested Configuration Options (`config/uom.php`)

### 🔁 Conversion Behavior

```php
'conversion' => [
    'default_precision' => 2, // Rounding precision for conversions
    'allow_custom_formulas' => true, // Enable user-defined formulas
    'fallback_to_base_unit' => true, // Normalize via base unit if direct path missing
],
```

---

### 📦 Packaging Logic

```php
'packaging' => [
    'max_depth' => 3, // Prevent excessive nesting (e.g., pallet → box → bottle → sachet)
    'allow_circular' => false, // Prevent circular packaging relationships
],
```

---

### 🧾 Audit Logging

```php
'audit' => [
    'log_conversions' => true, // Enable logging of conversion events
    'immutable_logs' => true, // Prevent editing of conversion logs
    'track_user' => true, // Attach user ID to logs (if available)
],
```

---

### 🧠 Unit Behavior

```php
'units' => [
    'enforce_unique_aliases' => true, // Prevent duplicate aliases
    'auto_slug' => true, // Generate slugs from unit/type names
    'default_base_unit_per_type' => true, // Enforce one base unit per UOMType
],
```

---

### 🧩 Customization & Extensibility

```php
'custom_units' => [
    'enabled' => true, // Allow users to define their own units
    'require_user_ownership' => true, // Tie custom units to users
],
```

---

### 🧪 Testing & Seeding

```php
'seeder' => [
    'enabled' => true,
    'default_sets' => ['metric_mass', 'imperial_length'], // Predefined unit sets
],
```

---

## 🛠️ Publishing the Config

In your `UOMServiceProvider`:

```php
$package->hasConfigFile();
```

Then users can publish it via:

```bash
php artisan vendor:publish --tag=laravel-uom-management-config
```

---
```php
return [

    /*
    |--------------------------------------------------------------------------
    | Conversion Settings
    |--------------------------------------------------------------------------
    */
    'conversion' => [
        'default_precision' => 2,
        'allow_custom_formulas' => true,
        'fallback_to_base_unit' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Packaging Settings
    |--------------------------------------------------------------------------
    */
    'packaging' => [
        'max_depth' => 3,
        'allow_circular' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'log_conversions' => true,
        'immutable_logs' => true,
        'track_user' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Unit Behavior
    |--------------------------------------------------------------------------
    */
    'units' => [
        'enforce_unique_aliases' => true,
        'auto_slug' => true,
        'default_base_unit_per_type' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Units
    |--------------------------------------------------------------------------
    */
    'custom_units' => [
        'enabled' => true,
        'require_user_ownership' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeder & Factory Defaults
    |--------------------------------------------------------------------------
    */
    'seeder' => [
        'enabled' => true,
        'default_sets' => ['metric_mass', 'imperial_length'],
    ],
];

```
### Registering the Config in Service Provider

```php
use Spatie\LaravelPackageTools\Package;

public function configurePackage(Package $package): void
{
    $package
        ->name('laravel-uom-management')
        ->hasConfigFile()
        ->hasMigrations([
            // your migrations
        ]);
}
```