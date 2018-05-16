<?php

namespace EasyBib\Tests\Mocks\OAuth2\Client;

use EasyBib\Guzzle\BearerAuthMiddleware;
use EasyBib\OAuth2\Client\AbstractSession;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class ResourceRequest
{
    /**
     * @var AbstractSession
     */
    private $session;

    /**
     * @param AbstractSession $session
     */
    public function __construct(AbstractSession $session)
    {
        $this->session = $session;
    }

    /**
     * @return RequestInterface
     */
    public function execute()
    {
        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        $stack->push($history);
        $stack->before('http_errors', function ($callable) {
            return new BearerAuthMiddleware($callable, $this->session);
        });

        $client = new Client(['handler' => $stack]);
        $client->get('http://example.org');

        return array_pop($container);
    }
}
