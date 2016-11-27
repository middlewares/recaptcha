# middlewares/recaptcha

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-scrutinizer]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabs Insight][ico-sensiolabs]][link-sensiolabs]

Middleware to use [google/recaptcha](https://github.com/google/recaptcha) library for spam prevention. Returns a `403` response if the request is not valid. More info about [Google reCAPTCHA](https://www.google.com/recaptcha).

## Requirements

* PHP >= 5.6
* A [PSR-7](https://packagist.org/providers/psr/http-message-implementation) http mesage implementation ([Diactoros](https://github.com/zendframework/zend-diactoros), [Guzzle](https://github.com/guzzle/psr7), [Slim](https://github.com/slimphp/Slim), etc...)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/recaptcha](https://packagist.org/packages/middlewares/recaptcha).

```sh
composer require middlewares/recaptcha
```

## Example

```php
$dispatcher = new Dispatcher([
	new Middlewares\Recaptcha($secretKey)
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Options

#### `__construct(string $secretKey)`

The secret API key of your app. You can register it at https://www.google.com/recaptcha/admin

#### `ipAttribute(string $ipAttribute)`

By default uses the `REMOTE_ADDR` server parameter to get the client ip. This option allows to use a request attribute. Useful to combine with a ip detection middleware, for example [client-ip](https://github.com/middlewares/client-ip):

```php
$dispatcher = new Dispatcher([
    //detect the client ip and save it in client-ip attribute
    new Middlewares\ClientIP(),

    //use that attribute
    (new Middlewares\Recaptcha($secretKey))
        ->ipAttribute('client-ip')
]);
```

## Helpers

### `Middlewares\Recaptcha::getCode(string $siteKey, array $options = [], $language = null)`

In order to ease the recaptcha code insertion, this helper is provided accepting the following arguments:

* `$siteKey` The public site key. You can get it on register the app.
* `$options` Widget options. More info in [recaptcha docs](https://developers.google.com/recaptcha/docs/display)
* `$language` To force the widget to render in a specific language. Auto-detects the user's language if unspecified.

```html
<form method="post">
    <input type="text" name="username">
    <input type="password" name="password">
    <?= Middlewares\Recaptcha::getCode($siteKey, ['size' => 'compact']) ?>
    <button type="submit">Register</button>
</form>
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/recaptcha.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/middlewares/recaptcha/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/middlewares/recaptcha.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/recaptcha.svg?style=flat-square
[ico-sensiolabs]: https://img.shields.io/sensiolabs/i/4070e9fb-5d51-4d6c-b28f-964cd8b6da0e.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/recaptcha
[link-travis]: https://travis-ci.org/middlewares/recaptcha
[link-scrutinizer]: https://scrutinizer-ci.com/g/middlewares/recaptcha
[link-downloads]: https://packagist.org/packages/middlewares/recaptcha
[link-sensiolabs]: https://insight.sensiolabs.com/projects/4070e9fb-5d51-4d6c-b28f-964cd8b6da0e
