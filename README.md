# middlewares/recaptcha

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
![Testing][ico-ga]
[![Total Downloads][ico-downloads]][link-downloads]

Middleware to use [google/recaptcha](https://github.com/google/recaptcha) library for spam prevention. Returns a `403` response if the request is not valid. More info about [Google reCAPTCHA](https://www.google.com/recaptcha).

## Requirements

* PHP >= 7.2
* A [PSR-7 http library](https://github.com/middlewares/awesome-psr15-middlewares#psr-7-implementations)
* A [PSR-15 middleware dispatcher](https://github.com/middlewares/awesome-psr15-middlewares#dispatcher)

## Installation

This package is installable and autoloadable via Composer as [middlewares/recaptcha](https://packagist.org/packages/middlewares/recaptcha).

```sh
composer require middlewares/recaptcha
```

## Example

```php
$dispatcher = new Dispatcher([
    new Middlewares\Recaptcha($secretKey),

    //in your view
    function () {
        echo '<div class="g-recaptcha" data-sitekey="XXX"></div>';
        echo '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>';
    }
]);

$response = $dispatcher->dispatch(new ServerRequest());
```

## Usage

You need a secret API key for your app. You can register it at https://www.google.com/recaptcha/admin

```php
$secretKey = 'Your-Secret-Key';

$recaptcha = new Middlewares\Recaptcha($secretKey);
```

Optionally, you can provide a `Psr\Http\Message\ResponseFactoryInterface` as the second argument to create the error responses (`403`). If it's not defined, [Middleware\Utils\Factory](https://github.com/middlewares/utils#factory) will be used to detect it automatically.

```php
$responseFactory = new MyOwnResponseFactory();

$recaptcha = new Middlewares\Recaptcha($secretKey, $responseFactory);
```

### ipAttribute

By default uses the `REMOTE_ADDR` server parameter to get the client ip. This option allows to use a request attribute. Useful to combine with a ip detection middleware, for example [client-ip](https://github.com/middlewares/client-ip):

```php
$dispatcher = new Dispatcher([
    //detect the client ip and save it in "ip" attribute
    (new Middlewares\ClientIP())->attribute('ip'),

    //use that attribute
    (new Middlewares\Recaptcha($secretKey))->ipAttribute('ip')
]);
```

---

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes and [CONTRIBUTING](CONTRIBUTING.md) for contributing details.

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/middlewares/recaptcha.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ga]: https://github.com/middlewares/recaptcha/workflows/testing/badge.svg
[ico-downloads]: https://img.shields.io/packagist/dt/middlewares/recaptcha.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/middlewares/recaptcha
[link-downloads]: https://packagist.org/packages/middlewares/recaptcha
