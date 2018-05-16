<?php

namespace EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams;

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
     * @var ServerConfig
     */
    private $serverConfig;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var Scope
     */
    private $scope;

    /**
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     * @param Scope $scope
     */
    public function __construct(
        ClientConfig $clientConfig,
        ServerConfig $serverConfig,
        ClientInterface $httpClient,
        Scope $scope
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->scope = $scope;
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
            $this->scope
        );
    }
}
