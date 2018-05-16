<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\TokenRequest;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

class TokenRequestTest extends TestCase
{
    public function testSend()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $tokenRequest = new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );

        $tokenResponse = $tokenRequest->send();

        $this->shouldHaveMadeATokenRequest();
        $this->assertInstanceOf(TokenResponse::class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }
}
