<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use GuzzleHttp\ClientInterface;

class TokenRefreshRequest
{
    const GRANT_TYPE = 'refresh_token';

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var ServerConfig
     */
    private $serverConfig;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param string $refreshToken
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     */
    public function __construct(
        $refreshToken,
        ServerConfig $serverConfig,
        ClientInterface $httpClient
    ) {
        $this->refreshToken = $refreshToken;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
    }

    /**
     * @return TokenResponse
     */
    public function send()
    {
        $url = $this->serverConfig->getParams()['token_endpoint'];

        $params = [
            [
                'name' => 'grant_type',
                'contents' => self::GRANT_TYPE,
            ],
            [
                'name' => 'refresh_token',
                'contents' => $this->refreshToken,
            ],
        ];

        $response = $this->httpClient->request('POST', $url, ['multipart' => $params]);

        return new TokenResponse($response);
    }
}
