<?php

namespace Middlewares\Tests;

use Middlewares\Recaptcha;
use Zend\Diactoros\ServerRequest;
use mindplay\middleman\Dispatcher;

class RecaptchaTest extends \PHPUnit_Framework_TestCase
{
    public function testRecaptcha()
    {
        $response = (new Dispatcher([
            new Recaptcha(uniqid()),
        ]))->dispatch(new ServerRequest([], [], '/', 'POST'));

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
