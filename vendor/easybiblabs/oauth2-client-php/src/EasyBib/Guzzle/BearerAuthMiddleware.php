<?php

namespace EasyBib\Guzzle;

use EasyBib\Guzzle\Exception\BearerErrorResponseException;
use EasyBib\OAuth2\Client\AbstractSession;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BearerAuthMiddleware
{
    /** @var callable  */
    private $nextHandler;

    /** @var AbstractSession */
    private $session;

    public function __construct(callable $nextHandler, AbstractSession $session)
    {
        $this->nextHandler = $nextHandler;
        $this->session = $session;
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $nextHandler = $this->nextHandler;

        if ($request->hasHeader('Authorization')) {
            return $nextHandler($request, $options);
        }

        $request = $request->withHeader('Authorization', sprintf('Bearer %s', $this->session->getToken()));

        return $nextHandler($request, $options)
            ->then(function (ResponseInterface $response) use ($request, $options) {
                $code = $response->getStatusCode();
                if ($code < 400) {
                    return $response;
                }

                if ($response->hasHeader("WWW-Authenticate")) {
                    throw BearerErrorResponseException::create($request, $response);
                }

                throw RequestException::create($request, $response);
            });
    }
}
