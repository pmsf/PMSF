<?php

namespace EasyBib\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\TokenRequestInterface;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use Firebase\JWT\JWT;
use GuzzleHttp\ClientInterface;

class TokenRequest implements TokenRequestInterface
{
    const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
    const EXPIRES_IN_TIME = 36000;
    const NOT_BEFORE_TIME = 96000;

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
        $baseTime
    ) {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
        $this->httpClient = $httpClient;
        $this->scope = $scope;
        $this->baseTime = $baseTime;
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
        $payload = [
            'scope' => $this->scope->getQuerystringParams()['scope'],
            'iss' => $this->clientConfig->getParams()['client_id'],
            'sub' => $this->clientConfig->getParams()['subject'],
            'aud' => $this->getTokenEndpoint(),
            'exp' => $this->baseTime + self::EXPIRES_IN_TIME,
            'nbf' => $this->baseTime - self::NOT_BEFORE_TIME,
            'iat' => $this->baseTime,
            'jti' => '',
            'typ' => '',
        ];

        $assertion = JWT::encode($payload, $this->clientConfig->getParams()['client_secret']);

        return [
            [
                'name' => 'grant_type',
                'contents' => self::GRANT_TYPE,
            ],
            [
                'name' => 'assertion',
                'contents' => $assertion,
            ],
        ];
    }

    /**
     * @return string
     */
    private function getTokenEndpoint()
    {
        return vsprintf('%s%s', [
            $this->httpClient->getConfig('base_uri'),
            $this->serverConfig->getParams()['token_endpoint'],
        ]);
    }
}
