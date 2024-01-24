# DB CONFIG

[![Latest Version on Packagist](https://img.shields.io/packagist/v/postare/db-config.svg?style=flat-square)](https://packagist.org/packages/postare/db-config)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/postare/db-config/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/postare/db-config/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/postare/db-config/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/postare/db-config/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/postare/db-config.svg?style=flat-square)](https://packagist.org/packages/postare/db-config)

This Filament plugin enables you to create dynamic configuration pages for your Laravel project.

![Screenshot](https://raw.githubusercontent.com/postare/db-config/main/screenshot.png)

> Tested on Laravel 10.x and Filament 3.x

## Installation

Install the package via Composer:

```bash
composer require postare/db-config
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="db-config-migrations"
php artisan migrate
```

## Usage

Create a configuration page using the following command along with the name of the page:

```bash
php artisan make:settings website
```

This will create a Filament Page and a corresponding view. Next, modify the page file to add the fields you wish to
display on the configuration page.

Example:

```php
namespace App\Filament\Pages;

use Filament\Forms\Form;
use Postare\DbConfig\AbstractPageSettings;
use Filament\Forms\Components\TextInput;

class WebsiteSettingsPage extends AbstractPageSettings
{
    public ?array $data = [];

    protected static ?string $title = 'Website Settings';

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected ?string $subheading = 'Manage your website configurations here.';

    protected static string $view = 'filament.config-pages.website';

    protected function settingName(): string
    {
        return 'website';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('site_name')->required(),
                // Add more fields here
            ])
            ->statePath('data');
    }
}
```

No additional steps are required. The package handles saving data to the database and retrieving it as needed.

## Accessing Saved Configurations

You can access the configurations in the following ways:

```php
// *Recommended* Helper method with optional default value
db_config('website.site_name', 'default value')

// Blade Directive
@db_config('website.site_name')

// Static Class
\Postare\DbConfig\DbConfig::get('website.site_name', 'default value');
```

## License

This package is open-sourced software licensed under the MIT License.
