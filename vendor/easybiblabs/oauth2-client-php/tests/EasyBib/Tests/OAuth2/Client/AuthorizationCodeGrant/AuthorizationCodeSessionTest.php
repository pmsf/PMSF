<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\AuthorizationCodeSession;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\StateStore;
use EasyBib\OAuth2\Client\TokenStore;
use EasyBib\Tests\Mocks\OAuth2\Client\ExceptionMockRedirector;
use EasyBib\Tests\Mocks\OAuth2\Client\ResourceRequest;
use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AuthorizationCodeSessionTest extends TestCase
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    /**
     * @var StateStore
     */
    private $stateStore;

    /**
     * @var AuthorizationCodeSession
     */
    private $oauthSession;

    public function setUp()
    {
        parent::setUp();

        $this->session = new Session(new MockArraySessionStorage());
        $this->session->set(StateStore::KEY_STATE, 'ABC123');

        $this->tokenStore = new TokenStore($this->session);
        $this->stateStore = new StateStore($this->session);
        $this->oauthSession = $this->createSession();
    }

    public function testGetTokenWhenNotSet()
    {
        $this->expectRedirectToAuthorizationEndpoint();
        $this->oauthSession->getToken();
    }

    public function testHandleAuthorizationResponse()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $this->oauthSession->handleAuthorizationResponse(
            $this->getAuthorization($this->stateStore->getState())
        );

        $this->shouldHaveMadeATokenRequest();
        $this->shouldHaveTokenInHeaderForResourceRequests($token);
    }

    public function testHandleAuthorizationResponseMissingState()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $this->expectStateException();

        $this->oauthSession->handleAuthorizationResponse(
            $this->getAuthorization(null)
        );
    }

    public function testHandleAuthorizationResponseWrongState()
    {
        $token = 'token_ABC123';
        $this->given->iAmReadyToRespondToATokenRequest($token, $this->scope, $this->mockHandler);

        $this->expectStateException();

        $this->oauthSession->handleAuthorizationResponse(
            $this->getAuthorization('some_random_thang')
        );
    }

    public function testResourceRequestWhenExpiredHavingRefreshToken()
    {
        $oldToken = 'ABC123';
        $newToken = 'XYZ987';
        $refreshToken = 'REFRESH_456';

        $this->given->iHaveATokenInSession($oldToken, $this->session);
        $this->given->iHaveARefreshToken($refreshToken, $this->session);
        $this->given->myTokenIsExpired($this->session);
        $this->given->iAmReadyToRespondToATokenRequest($newToken, $this->scope, $this->mockHandler);

        (new ResourceRequest($this->oauthSession))->execute()['request'];

        $this->shouldHaveMadeATokenRefreshRequest($refreshToken);
        $this->shouldHaveTokenInHeaderForResourceRequests($newToken);
    }

    private function shouldHaveTokenInHeaderForResourceRequests($token)
    {
        /** @var RequestInterface $lastRequest */
        $lastRequest = (new ResourceRequest($this->oauthSession))->execute()['request'];

        $this->assertEquals($token, $this->tokenStore->getToken());
        $this->assertTrue($lastRequest->hasHeader('Authorization'));
        $this->assertEquals('Bearer ' . $token, $lastRequest->getHeader('Authorization')[0]);
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

    private function expectRedirectToAuthorizationEndpoint()
    {
        $message = vsprintf(
            'Redirecting to %s?response_type=%s&state=%s&client_id=%s&redirect_uri=%s&scope=%s',
            [
                $this->apiBaseUrl . $this->serverConfig->getParams()['authorization_endpoint'],
                'code',
                $this->session->get(StateStore::KEY_STATE),
                'client_123',
                urlencode($this->clientConfig->getParams()['redirect_uri']),
                'USER_READ+DATA_READ_WRITE'
            ]
        );

        $exceptionClass = '\EasyBib\Tests\Mocks\OAuth2\Client\MockRedirectException';
        $this->setExpectedException($exceptionClass, $message);
    }

    /**
     * @return AuthorizationCodeSession
     */
    private function createSession()
    {
        $session = new AuthorizationCodeSession(
            $this->httpClient,
            new ExceptionMockRedirector(),
            $this->clientConfig,
            $this->serverConfig
        );

        $session->setTokenStore($this->tokenStore);
        $session->setStateStore($this->stateStore);
        $session->setScope($this->scope);

        return $session;
    }

    /**
     * @param string $state
     * @return AuthorizationResponse
     */
    private function getAuthorization($state)
    {
        $params = [
            'code' => 'ABC123',
        ];

        if ($state) {
            $params['state'] = $state;
        }

        return new AuthorizationResponse($params);
    }

    private function expectStateException()
    {
        $this->setExpectedException(
            '\EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\StateMismatchException'
        );
    }
}
