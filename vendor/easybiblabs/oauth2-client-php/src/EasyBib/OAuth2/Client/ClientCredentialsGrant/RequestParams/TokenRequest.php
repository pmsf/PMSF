<?php

namespace EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestInterface;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use GuzzleHttp\ClientInterface;

class TokenRequest implements TokenRequestInterface
{
    const GRANT_TYPE = 'client_credentials';

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
     * @return TokenResponse
     */
    public function send()
    {
        $url = $this->serverConfig->getParams()['token_endpoint'];
        $response = $this->httpClient->request('POST', $url, ['multipart' => $this->getParams()]);

        return new TokenResponse($response);
    }

    /**
     * @return array[]
     */
    private function getParams()
    {
        return [
            [
                'name' => 'grant_type',
                'contents' => self::GRANT_TYPE,
            ],
            [
                'name' => 'client_id',
                'contents' => $this->clientConfig->getParams()['client_id'],
            ],
            [
                'name' => 'client_secret',
                'contents' => $this->clientConfig->getParams()['client_secret'],
            ],
        ];
    }
}
