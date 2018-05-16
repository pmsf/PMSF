<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\HttpBasic;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasic\TokenRequest;
use GuzzleHttp\Psr7\MultipartStream;

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
            $this->scope
        );

        $tokenResponse = $tokenRequest->send();
        $class = '\EasyBib\OAuth2\Client\TokenResponse\TokenResponse';

        $this->shouldHaveMadeAnHttpBasicTokenRequest();
        $this->assertInstanceOf($class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }

    private function shouldHaveMadeAnHttpBasicTokenRequest()
    {
        $lastRequest = $this->mockHandler->getLastRequest();

        $configParams = $this->clientConfig->getParams();

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $expectedBody = new MultipartStream([
            [
                'name' => 'grant_type',
                'contents' => TokenRequest::GRANT_TYPE,
            ],
        ], $lastRequest->getBody()->getBoundary());

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals((string)$expectedBody, (string)$lastRequest->getBody());
        $this->assertEquals($expectedUrl, $lastRequest->getUri());
        $this->assertTrue($lastRequest->hasHeader('Authorization'));
        $this->assertEquals(
            'Basic '.base64_encode($configParams['client_id'].':'.$configParams['client_password']),
            $lastRequest->getHeader('Authorization')[0]
        );
    }
}
