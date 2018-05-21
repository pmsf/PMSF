<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\StateMismatchException;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\StateStore;
use EasyBib\OAuth2\Client\TokenStore;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthorizationCodeSession extends AbstractSession
{
    /**
     * @var StateStore
     */
    private $stateStore;

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
        $this->stateStore = new StateStore(new Session());
    }

    public function setStateStore(StateStore $stateStore)
    {
        $this->stateStore = $stateStore;
    }

    /**
     * @param AuthorizationResponse $authResponse
     * @throws StateMismatchException
     */
    public function handleAuthorizationResponse(AuthorizationResponse $authResponse)
    {
        if (!$this->stateStore->validateResponse($authResponse)) {
            throw new StateMismatchException('State does not match');
        }

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
        $params = [
            'response_type' => 'code',
            'state' => $this->stateStore->getState(),
        ] + $this->clientConfig->getParams();

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
