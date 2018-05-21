<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequestFactory;

class TokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new TokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );

        $class = '\EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequest';
        $this->assertInstanceOf($class, $tokenRequestFactory->create());
    }
}
