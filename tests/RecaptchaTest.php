<?php
declare(strict_types = 1);

namespace Middlewares\Tests;

use Middlewares\Recaptcha;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\TestCase;

class RecaptchaTest extends TestCase
{
    public function testRecaptcha(): void
    {
        $request = Factory::createServerRequest('POST', '/');

        $response = Dispatcher::run([
            new Recaptcha(uniqid()),
        ], $request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testIpAttribute(): void
    {
        $request = Factory::createServerRequest('POST', '/')->withAttribute('ip', '0.0.0.0');

        $response = Dispatcher::run([
            (new Recaptcha(uniqid()))->ipAttribute('ip'),
        ], $request);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testGetMethod(): void
    {
        $request = Factory::createServerRequest('GET', '/', ['REMOTE_ADDR' => '0.0.0.0']);

        $response = Dispatcher::run([
            (new Recaptcha(uniqid()))->ipAttribute('ip'),
        ], $request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
