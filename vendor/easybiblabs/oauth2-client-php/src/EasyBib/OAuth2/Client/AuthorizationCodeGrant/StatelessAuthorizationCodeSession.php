<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\TokenStore;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class StatelessAuthorizationCodeSession extends AbstractSession
{
    /**
     * @param ClientInterface $httpClient
     * @param RedirectorInterface $redirector
     * @param ClientConfig $clientConfig
     * @param ServerConfig $serverConfig
     */
    public function __construct(
        ClientInterface $httpClient,
        RedirectorInterface $redirector,
        ClientConfig $clientConfig,
        ServerConfig $serverConfig
    ) {
        $this->httpClient = $httpClient;
        $this->redirector = $redirector;
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;

        $this->tokenStore = new TokenStore(new Session());
    }

    /**
     * @param AuthorizationResponse $authResponse
     */
    public function handleAuthorizationResponse(AuthorizationResponse $authResponse)
    {
        $tokenRequest = new TokenRequest(
            $this->clientConfig,
            $this->serverConfig,
            $this->httpClient,
            $authResponse
        );

        $tokenResponse = $tokenRequest->send();
        $this->tokenStore->updateFromTokenResponse($tokenResponse);
    }

    /**
     * @return string
     */
    protected function getAuthorizeUrl()
    {
        $params = ['response_type' => 'code'] + $this->clientConfig->getParams();

        if ($this->scope) {
            $params += $this->scope->getQuerystringParams();
        }

        return vsprintf('%s%s%s%s', [
            $this->httpClient->getConfig('base_uri'),
            $this->serverConfig->getParams()['authorization_endpoint'],
            '?',
            http_build_query($params),
        ]);
    }
}
