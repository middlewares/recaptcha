<?php

namespace Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * Returns a recaptcha code.
     *
     * @param string      $siteKey
     * @param array       $options
     * @param string|null $language
     *
     * @return string
     */
    public static function getCode($siteKey, array $options = [], $language = null)
    {
        $data = sprintf('data-sitekey="%s"', $siteKey);

        foreach ($options as $name => $value) {
            $data .= sprintf(' data-%s="%s"', $name, $value);
        }

        $query = $language ? sprintf('?hl=%s', $language) : '';

        return <<<EOT
<div class="g-recaptcha" {$data}></div>
<script type="text/javascript" src="https://www.google.com/recaptcha/api.js{$query}"></script>
EOT;
    }

    /**
     * Constructor. Set the secret token.
     *
     * @param string $secret
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Set the attribute name to get the client ip.
     *
     * @param string $ipAttribute
     *
     * @return self
     */
    public function ipAttribute($ipAttribute)
    {
        $this->ipAttribute = $ipAttribute;

        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        if (!$this->isValid($request)) {
            return Utils\Factory::createResponse(403);
        }

        return $handler->handle($request);
    }

    /**
     * Check whether the request is valid.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function isValid(ServerRequestInterface $request)
    {
        $method = strtoupper($request->getMethod());

        if (in_array($method, ['GET', 'HEAD', 'CONNECT', 'TRACE', 'OPTIONS'], true)) {
            return true;
        }

        $recaptcha = new GoogleRecaptcha($this->secret);
        $data = $request->getParsedBody();

        $response = $recaptcha->verify(
            isset($data['g-recaptcha-response']) ? $data['g-recaptcha-response'] : '',
            $this->getIp($request)
        );

        return $response->isSuccess();
    }

    /**
     * Get the client ip.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getIp(ServerRequestInterface $request)
    {
        $server = $request->getServerParams();

        if ($this->ipAttribute !== null) {
            return $request->getAttribute($this->ipAttribute);
        }

        return isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '';
    }
}
