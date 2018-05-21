<?php

namespace EasyBib\OAuth2\Client;

use Symfony\Component\HttpFoundation\Session\Session;

class SimpleSession extends AbstractSession
{
    /**
     * @var TokenRequestFactoryInterface
     */
    private $tokenRequestFactory;

    /**
     * @param TokenRequestFactoryInterface $tokenRequestFactory
     * @param TokenStore $tokenStore
     */
    public function __construct(TokenRequestFactoryInterface $tokenRequestFactory, TokenStore $tokenStore = null)
    {
        $this->tokenRequestFactory = $tokenRequestFactory;
        $this->tokenStore = $tokenStore ? : new TokenStore(new Session());
    }

    /**
     * @return string
     */
    protected function doGetToken()
    {
        $token = $this->tokenStore->getToken();

        if ($token) {
            return $token;
        }

        $tokenRequest = $this->tokenRequestFactory->create();
        $tokenResponse = $tokenRequest->send();
        $this->tokenStore->updateFromTokenResponse($tokenResponse);

        return $this->tokenStore->getToken();
    }
}
