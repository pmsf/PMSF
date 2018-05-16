<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\SimpleSession;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequest;
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\TokenRequestFactory;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ResourceRequest;
use EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams\TestCase;
use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SimpleSessionTest extends TestCase
{
    /**
     * @var Session
     */
    private $tokenSession;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    /**
     * @var SimpleSession
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);
        $this->session = $this->createParamsSession();
    }

    public function testSetTokenStoreWhenRequestsAlreadyMade()
    {
        $token = 'ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $this->session->getToken();

        $this->setExpectedException('\LogicException');
        $this->session->setTokenStore(new TokenStore($this->tokenSession));
    }

    public function testGetTokenWhenNotSet()
    {
        $token = 'ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $this->session->getToken();

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenSet()
    {
        $token = 'ABC123';

        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenExpired()
    {
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->scope, $this->mockHandler);

        (new ResourceRequest($this->session))->execute();

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    private function shouldHaveTokenInHeaderForResourceRequests($token)
    {
        /** @var RequestInterface $lastRequest */
        $lastRequest = (new ResourceRequest($this->session))->execute()['request'];

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertTrue($lastRequest->hasHeader('Authorization'));
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization')[0]);
    }

    private function shouldHaveMadeATokenRequest()
    {
        $lastRequest = $this->mockHandler->getLastRequest();

        $expectedUrl = sprintf(
            '%s%s',
            $this->apiBaseUrl,
            $this->serverConfig->getParams()['token_endpoint']
        );

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($expectedUrl, $lastRequest->getUri());

        $requestBody = $lastRequest->getBody();
        $this->assertInstanceOf(MultipartStream::class, $requestBody);

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
        ], $requestBody->getBoundary());
        $this->assertEquals((string)$expectedBody, (string)$lastRequest->getBody());
    }

    /**
     * @return SimpleSession
     */
    private function createParamsSession()
    {
        $tokenRequestFactory = new TokenRequestFactory(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope
        );

        $session = new SimpleSession($tokenRequestFactory);
        $session->setTokenStore($this->tokenStore);

        return $session;
    }
}
