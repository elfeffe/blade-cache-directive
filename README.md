# Blade Cache Directive

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elfeffe/blade-cache-directive.svg?style=flat-square)](https://packagist.org/packages/elfeffe/blade-cache-directive)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/elfeffe/blade-cache-directive/run-tests?label=tests)](https://github.com/elfeffe/blade-cache-directive/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/elfeffe/blade-cache-directive/Check%20&%20fix%20styling?label=code%20style)](https://github.com/elfeffe/blade-cache-directive/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elfeffe/blade-cache-directive.svg?style=flat-square)](https://packagist.org/packages/elfeffe/blade-cache-directive)

Cache chunks of your Blade markup with ease.

## Installation

You can install the package via Composer:

```bash
composer require elfeffe/blade-cache-directive
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Elfeffe\BladeCacheDirective\BladeCacheDirectiveServiceProvider" --tag="blade-cache-directive-config"
```

This is the contents of the published config file:

```php
return [
    'ttl' => 3_600,
];
```

## Usage

This package adds a new `@cache` Blade directive. It accepts 2 arguments - the cache key and a TTL.

```blade
@cache('current_time', 30)
    {{ now() }}
@endcache
```

When used inside of a Blade template, the content between the 2 directives will be cached using Laravel's application cache. If a TTL (in seconds) isn't provided, the default TTL of **1 hour** will be used instead.

If you want to cache the content for a particular model, i.e. a `User` model, you can use string interpolation to change the key.

```blade
@cache("user_profile_{$user->id}")
    {{ $user->name }}
@endcache
```

When a new user is passed to this view, a separate cache entry will be created.

## Tags

The package also supports Laravel's cache tags. You can add tags to your cached content:

```blade
@cache('user_profile', null, ['users', 'profiles'])
    {{ $user->name }}
@endcache
```

This allows you to invalidate specific groups of cached content using Laravel's cache tag functionality:

```php
Cache::tags(['users'])->flush();
```

Note that cache tags are only available when using cache drivers that support tagging (Redis, Memcached).

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

- [Federico Reggiani](https://github.com/elfeffe)
- [Ryan Chandler](https://github.com/ryangjchandler)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
