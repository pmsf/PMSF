<?php

require_once("config/config.php");
require_once("DiscordRedirector.php");
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ClientConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\AuthorizationCodeSession;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\Scope;
use GuzzleHttp\Client;

class DiscordAuth
{
    protected $oauthSession;
    protected $resource;

    protected function setUpOAuth()
    {
        $httpClient = new Client(['base_uri' => 'https://discordapp.com']);
        $redirector = new DiscordRedirector($this);

        global $discordBotClientId, $discordBotClientSecret, $discordBotRedirectUri;

        $clientConfig = new ClientConfig([
            'client_id' => $discordBotClientId,
            'client_secret' => $discordBotClientSecret,
            'redirect_uri' => $discordBotRedirectUri
        ]);

        $serverConfig = new ServerConfig([
            'authorization_endpoint' => '/api/oauth2/authorize',
            'token_endpoint' => '/api/oauth2/token',
        ]);

        $this->oauthSession = new AuthorizationCodeSession(
            $httpClient,
            $redirector,
            $clientConfig,
            $serverConfig
        );

        $scope = new Scope(['identify', 'guilds']);
        $this->oauthSession->setScope($scope);
    }

    public function gotoDiscord()
    {
        $this->setUpOAuth();
        return $this->oauthSession->authorize();
    }

    public function handleAuthorizationResponse()
    {
        $this->setUpOAuth();
        $authorizationResponse = new AuthorizationResponse($_GET);
        $this->oauthSession->handleAuthorizationResponse($authorizationResponse);
    }

    private function setUpResource()
    {
        if ($this->resource != null) {
            return $this->resource;
        }
        $handler = \GuzzleHttp\HandlerStack::create();
        $handler->before('http_errors', function ($callable) {
            return new \EasyBib\Guzzle\BearerAuthMiddleware($callable, $this->oauthSession);
        });
        $this->resource = new \GuzzleHttp\Client([
            'base_uri' => 'https://discordapp.com',
            'handler' => $handler,
        ]);
    }

    public function get($uri)
    {
        $this->setUpResource();
        return $this->resource->get($uri)->getBody()->getContents();
    }
}
