<?php

namespace EasyBib\OAuth2\Client;

abstract class AbstractSession
{
    /**
     * @var \EasyBib\OAuth2\Client\TokenStore
     */
    protected $tokenStore;

    /**
     * @var bool
     */
    private $requestsAlreadyMade = false;

    /**
     * @return string
     */
    abstract protected function doGetToken();

    /**
     * @return string
     */
    public function getToken()
    {
        $this->requestsAlreadyMade = true;
        return $this->doGetToken();
    }

    /**
     * @param \EasyBib\OAuth2\Client\TokenStore $tokenStore
     * @throws \LogicException
     */
    public function setTokenStore(TokenStore $tokenStore)
    {
        if ($this->requestsAlreadyMade) {
            throw new \LogicException('Cannot set token store after requests already made');
        }

        $this->tokenStore = $tokenStore;
    }
}
