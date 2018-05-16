<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\Scope;
use GuzzleHttp\ClientInterface;

abstract class AbstractSession extends \EasyBib\OAuth2\Client\AbstractSession
{
    /**
     * @var RedirectorInterface
     */
    protected $redirector;

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

    /** @var ClientInterface */
    protected $httpClient;

    /**
     * @param AuthorizationResponse $authResponse
     */
    abstract public function handleAuthorizationResponse(AuthorizationResponse $authResponse);

    /**
     * @return string
     */
    abstract protected function getAuthorizeUrl();

    /**
     * @param Scope $scope
     */
    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
    }

    public function authorize()
    {
        $this->redirector->redirect($this->getAuthorizeUrl());
    }

    /**
     * @return string
     */
    protected function doGetToken()
    {
        $token = $this->tokenStore->getToken();

        if ($token) {
            return $token;
        }

        if ($this->tokenStore->isRefreshable()) {
            return $this->getRefreshedToken();
        }

        // redirects browser
        $this->authorize();
    }

    /**
     * @return string
     */
    protected function getRefreshedToken()
    {
        $refreshRequest = new TokenRefreshRequest(
            $this->tokenStore->getRefreshToken(),
            $this->serverConfig,
            $this->httpClient
        );

        $tokenResponse = $refreshRequest->send();
        $this->tokenStore->updateFromTokenResponse($tokenResponse);

        return $tokenResponse->getToken();
    }
}
