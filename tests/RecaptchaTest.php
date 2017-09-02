<?php

namespace Middlewares\Tests;

use PHPUnit\Framework\TestCase;
use Middlewares\Recaptcha;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class RecaptchaTest extends TestCase
{
    public function testRecaptcha()
    {
        $request = Factory::createServerRequest([], 'POST');

        $response = Dispatcher::run([
            new Recaptcha(uniqid()),
        ], $request);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCode()
    {
        $expected = '<div class="g-recaptcha" data-sitekey="XXX"></div>'."\n".
            '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>';

        $this->assertEquals($expected, Recaptcha::getCode('XXX'));
    }
}