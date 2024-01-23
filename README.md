# This is my package db-settings

[![Latest Version on Packagist](https://img.shields.io/packagist/v/postare/db-settings.svg?style=flat-square)](https://packagist.org/packages/postare/db-settings)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/postare/db-settings/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/postare/db-settings/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/postare/db-settings/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/postare/db-settings/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/postare/db-settings.svg?style=flat-square)](https://packagist.org/packages/postare/db-settings)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require postare/db-settings
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="db-settings-migrations"
php artisan migrate
```

## Usage

Creare una pagina di configurazione usando il seguente comando seguito dal nome della pagina:

```bash
php artisan make:settings website 
```

Questo creerà la Pagina Filament e la vista.
Adesso dovrai modificare il file della pagina per aggiungere i campi che vuoi mostrare nella pagina di configurazione.

```php
<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Postare\DbSettings\BaseSettings;

class WebsiteSettingsPage extends BaseSettings
{
    public ?array $data = [];

    protected static ?string $title = 'Website';

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected ?string $subheading = '';

    protected static string $view = 'filament.setting-pages.website';

    protected function settingName(): string
    {
        return 'website';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            // Add your fields here
                TextInput::make('site_name')
                    ->required(),
            ])
            ->statePath('data');
    }
}

```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
