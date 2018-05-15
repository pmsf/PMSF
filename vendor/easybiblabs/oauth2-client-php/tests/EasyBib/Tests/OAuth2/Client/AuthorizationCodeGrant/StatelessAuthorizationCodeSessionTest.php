<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\Guzzle\BearerAuthMiddleware;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\StatelessAuthorizationCodeSession;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ExceptionMockRedirector;
use EasyBib\Tests\Mocks\OAuth2\Client\ResourceRequest;
use Guzzle\Http\Client;
use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class StatelessAuthorizationCodeSessionTest extends TestCase
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
     * @var StatelessAuthorizationCodeSession
     */
    private $session;

    public function setUp()
    {
        parent::setUp();

        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);
        $this->session = $this->createSession();
    }

    public function testGetTokenWhenNotSet()
    {
        $this->expectRedirectToAuthorizationEndpoint();
        $this->session->getToken();
    }

    public function testResourceRequestWhenSet()
    {
        $token = 'ABC123';

        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testResourceRequestWhenExpiredHavingRefreshToken()
    {
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';
        $refreshToken = 'REFRESH_456';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->iHaveARefreshToken($refreshToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->scope, $this->mockHandler);

        $lastRequest = (new ResourceRequest($this->session))->execute()['request'];

        $this->shouldHaveMadeATokenRefreshRequest($refreshToken);
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    public function testResourceRequestWhenExpiredHavingNoRefreshToken()
    {
        $oldToken = 'ABC123';

        $this->given->iHaveATokenInSession($oldToken, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);

        $this->expectRedirectToAuthorizationEndpoint();
        (new ResourceRequest($this->session))->execute();
    }

    public function testHandleAuthorizationResponse()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $this->session->handleAuthorizationResponse($this->authorization);

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    /**
     * @param string $refreshToken
     */
    private function shouldHaveMadeATokenRefreshRequest($refreshToken)
    {
        $lastRequest = $this->mockHandler->getLastRequest();

        $this->assertEquals(
            $this->apiBaseUrl . $this->serverConfig->getParams()['token_endpoint'],
            (string)$lastRequest->getUri()
        );

        $this->assertEquals('POST', $lastRequest->getMethod());
        $expectedBody = new MultipartStream([
            [
                'name' => 'grant_type',
                'contents' => 'refresh_token',
            ],
            [
                'name' => 'refresh_token',
                'contents' => $refreshToken,
            ],
        ], $lastRequest->getBody()->getBoundary());

        $this->assertEquals((string)$expectedBody, (string)$lastRequest->getBody());
    }

    private function shouldHaveTokenInHeaderForResourceRequests($token)
    {
        /** @var RequestInterface $lastRequest */
        $lastRequest = (new ResourceRequest($this->session))->execute()['request'];

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertTrue($lastRequest->hasHeader('Authorization'));
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization')[0]);
    }

    private function expectRedirectToAuthorizationEndpoint()
    {
        $message = vsprintf(
            'Redirecting to %s?response_type=%s&client_id=%s&redirect_uri=%s&scope=%s',
            [
                $this->apiBaseUrl . $this->serverConfig->getParams()['authorization_endpoint'],
                'code',
                'client_123',
                urlencode($this->clientConfig->getParams()['redirect_uri']),
                'USER_READ+DATA_READ_WRITE',
            ]
        );

        $exceptionClass = '\EasyBib\Tests\Mocks\OAuth2\Client\MockRedirectException';
        $this->setExpectedException($exceptionClass, $message);
    }

    /**
     * @return StatelessAuthorizationCodeSession
     */
    private function createSession()
    {
        $session = new StatelessAuthorizationCodeSession(
            $this->httpClient,
            new ExceptionMockRedirector(),
            $this->clientConfig,
            $this->serverConfig
        );

        $session->setTokenStore($this->tokenStore);
        $session->setScope($this->scope);

        return $session;
    }
}
