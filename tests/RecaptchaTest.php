<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\Recaptcha;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class RecaptchaTest extends TestCase
{
    public function testRecaptcha()
    {
        $request = Factory::createServerRequest([], 'POST');

        $response = Dispatcher::run([
            new Recaptcha(uniqid()),
        ], $request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testIpAttribute()
    {
        $request = Factory::createServerRequest([], 'POST')->withAttribute('ip', '0.0.0.0');

        $response = Dispatcher::run([
            (new Recaptcha(uniqid()))->ipAttribute('ip'),
        ], $request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testGetMethod()
    {
        $request = Factory::createServerRequest(['REMOTE_ADDR' => '0.0.0.0'], 'GET');

        $response = Dispatcher::run([
            (new Recaptcha(uniqid()))->ipAttribute('ip'),
        ], $request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCode()
    {
        $expected = '<div class="g-recaptcha" data-sitekey="XXX" data-foo="bar"></div>'."\n".
            '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>';

        $this->assertEquals($expected, Recaptcha::getCode('XXX', ['foo' => 'bar']));
    }
}
