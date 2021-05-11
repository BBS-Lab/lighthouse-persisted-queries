# Lighthouse persisted queries middleware

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bbs-lab/lighthouse-persisted-queries.svg?style=flat-square)](https://packagist.org/packages/bbs-lab/lighthouse-persisted-queries)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bbs-lab/lighthouse-persisted-queries/run-tests?label=tests)](https://github.com/bbs-lab/lighthouse-persisted-queries/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bbs-lab/lighthouse-persisted-queries/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bbs-lab/lighthouse-persisted-queries/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/bbs-lab/lighthouse-persisted-queries.svg?style=flat-square)](https://packagist.org/packages/bbs-lab/lighthouse-persisted-queries)


This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require bbls-lab/lighthouse-persisted-queries
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="BBSLab\LighthousePersistedQueries\LighthousePersistedQueriesServiceProvider" --tag="lighthouse-persisted-queries-config"
```

This is the contents of the published config file:

```php
return [
    'cache' => [

        'prefix' => env('LPQ_CACHE_PREFIX', 'persisted_query'),

        'ttl' => env('LPQ_CACHE_TTL', 0),

        'max-age' => env('LPQ_CACHE_MAX_AGE', 86400),

    ],

    'excluded_operations' => [
        //
    ],

];
```

## Usage

To handle persisted queries you add the `BBSLab\LighthousePersistedQueries\PersistsQuery` middleware to the lighthouse middleware configuration section.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Mikaël Popowicz](https://github.com/mikaelpopowicz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
