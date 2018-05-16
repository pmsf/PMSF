<?php

namespace EasyBib\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestFactoryInterface;
use GuzzleHttp\ClientInterface;

class TokenRequestFactory implements TokenRequestFactoryInterface
{
    /**
     * @var ClientConfig
     */
    private $clientConfig;

    /**
     * @var \EasyBib\OAuth2\Client\ServerConfig
     */
    private $serverConfig;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var \EasyBib\OAuth2\Client\Scope
     */
    private $scope;

    /**
     * @var int
     */
    private $baseTime;

    /**
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     * @param Scope $scope
     * @param int $baseTime
     */
    public function __construct(
        ClientConfig $clientConfig,
        ServerConfig $serverConfig,
        ClientInterface $httpClient,
        Scope $scope,
        $baseTime = null
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->scope = $scope;
        $this->baseTime = $baseTime ?: time();
    }

    /**
     * @return TokenRequest
     */
    public function create()
    {
        return new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $this->scope,
            $this->baseTime
        );
    }
}
