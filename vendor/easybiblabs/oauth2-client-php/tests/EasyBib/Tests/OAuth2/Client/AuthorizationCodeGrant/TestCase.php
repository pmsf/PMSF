<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ClientConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\Tests\OAuth2\Client\Given;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\MultipartStream;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Given
     */
    protected $given;

    /**
     * @var string
     */
    protected $apiBaseUrl = 'http://data.easybib.example.com';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var ClientConfig
     */
    protected $clientConfig;

    /**
     * @var ServerConfig
     */
    protected $serverConfig;

    /**
     * @var AuthorizationResponse
     */
    protected $authorization;

    /**
     * @var Scope
     */
    protected $scope;

    /** @var MockHandler */
    protected $mockHandler;

    public function setUp()
    {
        parent::setUp();

        $this->given = new Given();

        $this->clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'redirect_uri' => 'http://myapp.example.com/',
        ]);

        $this->serverConfig = new ServerConfig([
            'authorization_endpoint' => '/oauth/authorize',
            'token_endpoint' => '/oauth/token',
        ]);

        $this->mockHandler = new MockHandler();
        $this->httpClient = new Client([
            'base_uri' => $this->apiBaseUrl,
            'handler' => $this->mockHandler,
        ]);

        $this->authorization = new AuthorizationResponse(['code' => 'ABC123']);
        $this->scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
    }

    protected function shouldHaveMadeATokenRequest()
    {
        $lastRequest = $this->mockHandler->getLastRequest();

        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals($this->apiBaseUrl . '/oauth/token', $lastRequest->getUri());

        $requestBody = $lastRequest->getBody();
        $expectedBody = new MultipartStream([
            [
                'name' => 'grant_type',
                'contents' => 'authorization_code',
            ],
            [
                'name' => 'code',
                'contents' => $this->authorization->getCode(),
            ],
            [
                'name' => 'redirect_uri',
                'contents' => $this->clientConfig->getParams()['redirect_uri'],
            ],
            [
                'name' => 'client_id',
                'contents' => $this->clientConfig->getParams()['client_id'],
            ],
        ], $requestBody->getBoundary());

        $this->assertEquals((string)$expectedBody, (string)$requestBody);
    }
}
