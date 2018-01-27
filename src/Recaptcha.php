<?php
declare(strict_types = 1);

namespace Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReCaptcha\ReCaptcha as GoogleRecaptcha;

class Recaptcha implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var string|null
     */
    private $ipAttribute;

    /**
     * Constructor. Set the secret token.
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Set the attribute name to get the client ip.
     */
    public function ipAttribute(string $ipAttribute): self
    {
        $this->ipAttribute = $ipAttribute;

        return $this;
    }

    /**
     * Process a server request and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isValid($request)) {
            return Utils\Factory::createResponse(403);
        }

        return $handler->handle($request);
    }

    /**
     * Check whether the request is valid.
     */
    private function isValid(ServerRequestInterface $request): bool
    {
        $method = strtoupper($request->getMethod());

        if (in_array($method, ['GET', 'HEAD', 'CONNECT', 'TRACE', 'OPTIONS'], true)) {
            return true;
        }

        $recaptcha = new GoogleRecaptcha($this->secret);
        $data = $request->getParsedBody();

        $response = $recaptcha->verify(
            $data['g-recaptcha-response'] ?? '',
            $this->getIp($request)
        );

        return $response->isSuccess();
    }

    /**
     * Get the client ip.
     */
    private function getIp(ServerRequestInterface $request): string
    {
        $server = $request->getServerParams();

        if ($this->ipAttribute !== null) {
            return $request->getAttribute($this->ipAttribute);
        }

        return isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '';
    }
}
