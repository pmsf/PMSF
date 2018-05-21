<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\TokenRequestInterface;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use GuzzleHttp\ClientInterface;

class TokenRequest implements TokenRequestInterface
{
    const GRANT_TYPE = 'authorization_code';

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
     * @var AuthorizationResponse
     */
    private $authorizationResponse;

    /**
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     * @param ClientInterface $httpClient
     * @param AuthorizationResponse $authorization
     */
    public function __construct(
        ClientConfig $clientConfig,
        ServerConfig $serverConfig,
        ClientInterface $httpClient,
        AuthorizationResponse $authorization
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->authorizationResponse = $authorization;
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
     * @return array
     */
    private function getParams()
    {
        $clientConfig = $this->clientConfig->getParams();
        $params = [
            [
                'name' => 'grant_type',
                'contents' => self::GRANT_TYPE,
            ],
            [
                'name' => 'code',
                'contents' => $this->authorizationResponse->getCode(),
            ],
            [
                'name' => 'redirect_uri',
                'contents' => $clientConfig['redirect_uri'],
            ],
            [
                'name' => 'client_id',
                'contents' => $clientConfig['client_id'],
            ],
        ];
        $addOptionalParam = function ($key) use (&$params, $clientConfig) {
            if (isset($clientConfig[$key])) {
                $params[] = [
                    'name' => $key,
                    'contents' => $clientConfig[$key],
                ];
            }
        };
        $addOptionalParam('client_secret');
        $addOptionalParam('resource');
        return $params;
    }
}
