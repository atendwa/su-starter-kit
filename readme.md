# Beat Spam Bots with Laravel Honeypot

[![Latest Version on Packagist](https://img.shields.io/packagist/v/atendwa/laravel-honeypot.svg/badge.svg)](https://packagist.org/packages/atendwa/laravel-honeypot)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=badge-rounded)](licence.md)

[![run-tests](https://github.com/spatie/laravel-varnish/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/laravel-varnish/actions/workflows/run-tests.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/atendwa/laravel-honeypot.svg?style=flat-square)](https://scrutinizer-ci.com/g/atendwa/laravel-honeypot)
[![Code Style Status](https://img.shields.io/github/actions/workflow/status/atendwa/laravel-honeypot/php-cs-fixer.yml?label=code%20style)](https://github.com/atendwa/laravel-honeypot/actions/workflows/php-cs-fixer.yml)

Laravel Honeypot is a package designed to help protect your Laravel applications from spam bots by adding a hidden form field that, when filled, indicates the submission is likely from a bot. It provides a simple and effective method to reduce spam submissions without inconveniencing genuine users.

### Installation

You can install the package via composer:

```bash
composer require atendwa/laravel-honeypot
```

### Configuration

You can override the default options for the honeypot. First publish the `honeypot.php` configuration file:
```bash
  php artisan vendor:publish --provider="Atendwa\SuStarterKit\HoneypotServiceProvider" --tag="config"
```

Add the following variables in your **.env** file to use you custom configurations

```
HONEYPOT_ENABLED=TRUE

HONEYPOT_INPUT_NAME=mobile

HONEYPOT_TIME_INPUT_NAME=time_field

HONEYPOT_MINIMUM_SUBMISSION_DURATION=1 #time in seconds
```

### Usage

1. Add the `prevent-spam` middleware to the routes you want to protect from spam bots.
    ```php
    Route::post('/submit-comment', [commentController::class, 'store'])
        ->middleware('prevent-spam');
    ```
   
2. Add the `<x-honeypot::honeypot-fields/>` blade component to the form body
    ```php
    <form action="/submit-comment" method="POST">
        @csrf
   
        <x-honeypot::honeypot-fields/>
        
        // other input fields
    </form>
    ```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](changelog) for more information what has changed recently.

### Contributing

Please see [CONTRIBUTING](contributing) for details.

### Security

If you discover any security related issues, please email tendwa.am@gmail.com instead of using the issue tracker.

### Credits

-   [Anthony Tendwa Michael](https://github.com/atendwa)

### License

The MIT License (MIT). Please see [License File](licence) for more information.
