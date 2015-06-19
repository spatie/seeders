# Seeders

[![Latest Version on Packagist](https://img.shields.io/github/release/spatie-custom/seeders.svg?style=flat-square)](https://packagist.org/packages/spatie-custom/seeders)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie-custom/seeders.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie-custom/seeders)

Database seeders for our laravel applications.

## Install

This package is custom built for [Spatie](https://spatie.be) projects and is therefore not registered on packagist. 
In order to install it via composer you must specify this extra repository in `composer.json`:

```json
"repositories": [ { "type": "composer", "url": "https://satis.spatie.be/" } ]
```

You can install the package via composer:
``` bash
$ composer require spatie/seeders
```

## Overview

This package provides the base database seeders for our laravel applications. The `Spatie\Seeders\DatabaseSeeder` class adds some extra utility to laravel's seeder.

Other classes are for recurring, specific parts of our application.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
