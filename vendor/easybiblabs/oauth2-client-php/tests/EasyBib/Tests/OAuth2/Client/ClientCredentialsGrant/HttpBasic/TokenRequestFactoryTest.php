<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\HttpBasic;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasic\TokenRequestFactory;

class TokenRequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $tokenRequestFactory = new TokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $class = '\EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasic\TokenRequest';
        $this->assertInstanceOf($class, $tokenRequestFactory->create());
    }
}
