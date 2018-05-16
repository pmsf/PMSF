<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequestFactory;

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

        $class = '\EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequest';
        $this->assertInstanceOf($class, $tokenRequestFactory->create());
    }
}
