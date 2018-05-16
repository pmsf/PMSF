<?php

namespace EasyBib\Tests\OAuth2\Client\ClientCredentialsGrant\RequestParams;

use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\ClientConfig;
use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\Tests\OAuth2\Client\Given;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
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
            'client_secret' => 'secret_456',
        ]);

        $this->serverConfig = new ServerConfig([
            'token_endpoint' => '/oauth/token',
        ]);

        $this->mockHandler = new MockHandler();
        $this->httpClient = new Client([
            'base_uri' => $this->apiBaseUrl,
            'handler' => $this->mockHandler,
        ]);

        $this->scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
    }
}
