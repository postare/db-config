# DB Config

[![Latest Version on Packagist](https://img.shields.io/packagist/v/postare/db-config.svg?style=flat-square)](https://packagist.org/packages/postare/db-config)
[![Total Downloads](https://img.shields.io/packagist/dt/postare/db-config.svg?style=flat-square)](https://packagist.org/packages/postare/db-config)

DB Config is a Filament plugin that provides a simple, database-backed key/value store for application settings, along with a streamlined way to build settings pages using Filament. It exposes a clean API for reading and writing values and uses transparent caching under the hood.

It is framework-friendly, requires no custom Eloquent models in your app, and persists values as JSON in a dedicated table.

## Requirements

- PHP version supported by your Laravel installation
- Laravel 10, 11 or 12
- A database engine with JSON support (MySQL 5.7+, MariaDB 10.2.7+, PostgreSQL, SQLite recent versions)
- Filament 3.x or 4.x (see Compatibility)

## Installation

Install the package via Composer (choose the version matching your Laravel version):

```bash
composer require postare/db-config:^2.0 # Laravel 10
composer require postare/db-config:^3.0 # Laravel 11
composer require postare/db-config:^4.0 # Laravel 12
```

Publish and run the migration:

```bash
php artisan vendor:publish --tag="db-config-migrations"
php artisan migrate
```

This creates a `db_config` table used to store your settings.

## Compatibility

- Laravel 10 + Filament 3 → `postare/db-config:^2.0`
- Laravel 11 + Filament 3 → `postare/db-config:^3.0`
- Laravel 12 + Filament 4 → `postare/db-config:^4.0`

## Usage of the `make:db_config` command

The package provides an Artisan generator that quickly creates a Filament settings page (Page class + Blade view).

Command:

```bash
php artisan make:db_config {name} {panel?}
```

Parameters:

- `name`: the settings group name (e.g. `website`). It is used to generate the view name and the class name (singular, capitalized).
- `panel` (optional): the Filament panel to create the page in (e.g. `Admin`). If omitted the default panel is used.

Examples:

```bash
php artisan make:db_config website
php artisan make:db_config website admin
```

What is generated:

- A Page class at `app/Filament/{Panel}/Pages/{Name}Settings.php` (the class name is the singular form of `{name}` + `Settings`, e.g. `WebsiteSettings.php`).
- A Blade view at `resources/views/filament/config-pages/{slug-name}-settings.blade.php` (the view name is a slugified version of the `name` with a `-settings` suffix).

Behavior:

- The command does not overwrite existing files: if the class or the view already exist it will warn and leave the files intact.
- Names are normalized: the class uses the singular form of the provided name, the view is slugified (spaces and special characters are converted).

Note: the generated class extends `Postare\DbConfig\AbstractPageSettings` and the view is placed under `resources/views/filament/config-pages/`.

## How it works

Settings are organized by a two-part key: `group.setting`, with optional nested sub-keys (e.g. `group.setting.nested.key`).

Under the hood:

- Settings are stored in a single row per `(group, key)` with the JSON payload in the `settings` column.
- Reads are cached forever under the cache key `db-config.{group}.{setting}`.
- Writes clear the corresponding cache entry to keep reads fresh.

## Filament integration

This package ships with an Artisan generator and an abstract Page class to quickly scaffold Filament settings pages.

Generate a settings page (and its Blade view):

```bash
php artisan make:db_config website            # default panel
php artisan make:db_config website admin      # specific panel (e.g. Admin)
```

What gets generated:

- A Page class in `app/Filament/{Panel}/Pages/*SettingsPage.php` that extends `Postare\DbConfig\AbstractPageSettings`.
- A Blade view at `resources/views/filament/config-pages/{name}.blade.php` which renders the page content.

Page lifecycle and saving:

- On `mount()`, the page loads all settings for the given group (defined by `settingName()`) via `DbConfig::getGroup()` and fills the page content state.
- A built-in header action “Save” persists the current state by calling `DbConfig::set("{group}.{key}", $value)` for each top-level key present in the page content.

Defining the page content:

- Implement `protected function settingName(): string` to define the group name (e.g. `website`).
- Implement `public function content(Schema $schema): Schema` and return your content schema.
- Set `->statePath('data')` so the page state is bound to the `$data` property and saved correctly.

Example page content (Filament schema):

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema; // or import the correct Schema depending on your setup

public function content(Schema $schema): Schema
{
    return $schema
        ->components([
            TextInput::make('site_name')->required(),
            // ... other inputs
        ])
        ->statePath('data');
}
```

## Database schema

The `db_config` table contains:

- `id` (bigint, primary key)
- `group` (string)
- `key` (string)
- `settings` (json, nullable)
- `created_at`, `updated_at` (timestamps)

There is a unique index on (`group`, `key`). Timestamps are present but not used by the package logic and may remain null depending on your database defaults.

## API

The package exposes a minimal API for interacting with settings.

Read a value (helper):

```php
db_config('website.site_name', 'Default Name');
```

Read a value (class):

```php
\Postare\DbConfig\DbConfig::get('website.site_name', 'Default Name');
```

Write a value:

```php
\Postare\DbConfig\DbConfig::set('website.site_name', 'Acme Inc.');
```

Read an entire group as associative array:

```php
\Postare\DbConfig\DbConfig::getGroup('website');
// => [ 'site_name' => 'Acme Inc.', 'contact' => ['email' => 'info@acme.test'] ]
```

Facade (optional):

```php
\Postare\DbConfig\Facades\DbConfig::get('website.site_name');
```

> The `db_config()` helper is auto-registered by the package and is the recommended way to read values in application code.

## Keys and nested data

- Keys are split by dots. The first segment is the `group`, the second is the top-level `setting`, and any remaining segments are treated as nested keys inside the stored JSON.
- Example: `profile.preferences.theme` stores/reads from row `(group=profile, key=preferences)` and resolves the nested path `theme` inside the JSON payload.
- Avoid using group-only keys (e.g. `profile`) — always specify at least `group.setting`.

Examples:

```php
// Store a nested structure
\Postare\DbConfig\DbConfig::set('profile.preferences', [
    'theme' => 'dark',
    'notifications' => ['email' => true, 'sms' => false],
]);

// Read a nested value with default
db_config('profile.preferences.theme', 'light'); // 'dark'

// Read a missing nested value
db_config('profile.preferences.timezone', 'UTC'); // 'UTC'
```

## Caching behavior

- Reads are cached forever per `(group, setting)` to minimize database traffic.
- `DbConfig::set()` automatically clears the cache for the affected `(group, setting)` pair.
- When debugging, you can clear the framework cache (`php artisan cache:clear`) to reset all cached values.

## Return values and defaults

- If a value or nested path does not exist, the provided default is returned.
- If the stored JSON value is `null`, the default is returned.
- `getGroup()` returns an associative array of all settings for the group, or an empty array if none exist.

## Database engines

This package stores settings as JSON. Ensure your chosen database supports JSON columns. For SQLite (common in tests), JSON is stored as text and works transparently for typical use cases.

## Security considerations

- Do not store secrets that belong in environment variables or the configuration cache (API keys, DB credentials). Use this package for runtime-editable application settings (e.g. labels, feature flags, contact info).
- Values are not encrypted by default. If you need encryption, wrap reads/writes with your own encryption layer before passing to the API.

## Versioning

This package follows semantic versioning. Use a version constraint compatible with your Laravel version as shown in the installation section.

## License

The MIT License (MIT). See the LICENSE file for more details.
