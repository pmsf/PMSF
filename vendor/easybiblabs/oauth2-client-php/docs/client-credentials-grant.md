# Client Credentials Grant

In this grant type, your client has a privileged ID and password/secret arranged with the
OAuth2 provider. There is no input required by the user in this grant type.

[The spec](http://tools.ietf.org/html/rfc6749#section-4.4) describes two modes
of authenticating a client.

## HTTP Basic client authentication

```php
use EasyBib\OAuth2\Client\ClientCredentialsGrant\HttpBasic\ClientConfig;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\SimpleSession;
use EasyBib\OAuth2\Client\Scope;
use GuzzleHttp\Client;

class MyWebController
{
    protected $resourceClient;

    private function setUpOAuth()
    {
        // your application's settings for the OAuth2 provider
        $clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_password' => 'password_456',
        ]);

        // the OAuth2 provider's settings
        $serverConfig = new ServerConfig([
            'token_endpoint' => '/oauth/token',
        ]);

        $oauthHttpClient = new Client(['base_uri' => 'http://myoauth2provider.example.com']);

        $scope = new Scope(['USER_DATA_READ']);

        $tokenRequestFactory = new TokenRequestFactory(
            $clientConfig,
            $serverConfig,
            $oauthHttpClient,
            $scope
        );

        $session = new SimpleSession($tokenRequestFactory);
        $handler = \GuzzleHttp\HandlerStack::create()
        $stackHandler->before('http_errors', function ($callable) use ($session) {
            return new \EasyBib\Guzzle\BearerAuthMiddleware($callable, $session);
        });
        $this->resourceHttpClient = new \GuzzleHttp\Client([
            'base_uri' => 'http://coolresources.example.com',
            'handler' => $handler,
        ]);
    }

    public function fooAction()
    {
        $apiRequest = $this->resourceHttpClient->get('/some/resource');

        // ...
    }
}
```

## Request body parameter client authentication

```php
use EasyBib\OAuth2\Client\ClientCredentialsGrant\RequestParams\ClientConfig;
use EasyBib\OAuth2\Client\ServerConfig;
use EasyBib\OAuth2\Client\SimpleSession;
use EasyBib\OAuth2\Client\Scope;
use GuzzleHttp\Client;

class MyWebController
{
    protected $resourceClient;

    private function setUpOAuth()
    {
        // your application's settings for the OAuth2 provider
        $clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'client_secret' => 'secret_456',
        ]);

        // the OAuth2 provider's settings
        $serverConfig = new ServerConfig([
            'token_endpoint' => '/oauth/token',
        ]);

        $oauthHttpClient = new Client(['base_uri' => 'http://myoauth2provider.example.com']);

        $scope = new Scope(['USER_DATA_READ']);

        $tokenRequestFactory = new TokenRequestFactory(
            $clientConfig,
            $serverConfig,
            $oauthHttpClient,
            $scope
        );

        $handler = \GuzzleHttp\HandlerStack::create()
        $stackHandler->before('http_errors', function ($callable) use ($session) {
            return new \EasyBib\Guzzle\BearerAuthMiddleware($callable, $session);
        });
        $this->resourceHttpClient = new \GuzzleHttp\Client([
            'base_uri' => 'http://coolresources.example.com',
            'handler' => $handler,
        ]);
    }

    public function fooAction()
    {
        $apiRequest = $this->resourceHttpClient->get('/some/resource');

        // ...
    }
}
```
