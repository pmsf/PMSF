<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequest;
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

        $this->shouldHaveMadeAParamsTokenRequest();
        $this->assertInstanceOf($class, $tokenResponse);
        $this->assertEquals($token, $tokenResponse->getToken());
    }

    private function shouldHaveMadeAParamsTokenRequest()
    {
        $lastRequest = $this->mockHandler->getLastRequest();

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedUrl, $lastRequest->getUri());
        $this->assertInstanceOf(MultipartStream::class, $lastRequest->getBody());

        $expectedBody = new MultipartStream([
            [
                'name' => 'grant_type',
                'contents' => TokenRequest::GRANT_TYPE,
            ],
            [
                'name' => 'client_id',
                'contents' => $this->clientConfig->getParams()['client_id'],
            ],
            [
                'name' => 'client_secret',
                'contents' => $this->clientConfig->getParams()['client_secret'],
            ],
        ], $lastRequest->getBody()->getBoundary());

        $this->assertEquals((string)$expectedBody, (string)$lastRequest->getBody());
    }
}
