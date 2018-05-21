<?php

namespace EasyBib\Tests\Mocks\OAuth2\Client;

use EasyBib\OAuth2\Client\TokenRequestFactoryInterface;
use EasyBib\OAuth2\Client\TokenRequestInterface;

class MockTokenRequestFactory implements TokenRequestFactoryInterface
{
    /**
     * @throws \BadMethodCallException
     * @return TokenRequestInterface
     */
    public function create()
    {
        throw new \BadMethodCallException('create() not yet implemented');
    }
}
