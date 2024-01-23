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

You can publish the config file with:

```bash
php artisan vendor:publish --tag="db-settings-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="db-settings-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$dbSettings = new Postare\DbSettings();
echo $dbSettings->echoPhrase('Hello, Postare!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Francesco](https://github.com/postare)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
