# Authorization Code Grant

During the initial authorization step of the OAuth2 transaction, your app will
need to redirect the user to the OAuth2 server. After authorization, the OAuth
server will redirect your user back to you.

In order for this OAuth2 client to initiate the redirect, you will need to
implement our RedirectorInterface within the context of your application. That
may be as simple as calling `header()` to send the Location, or it may involve
a call to your web framework.

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\RedirectorInterface;

class MyRedirector implements RedirectorInterface
{
    private $controller;

    public function __construct(MyWebController $controller)
    {
        $this->controller = $controller;
    }

    public function redirect($url)
    {
        // does something which eventually calls header() to redirect user
        $this->controller->redirect($url);
    }
}
```

First, instantiate the basic objects and use them to create an OAuth2 Session.

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ClientConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\AuthorizationCodeSession;
use EasyBib\OAuth2\Client\Scope;
use GuzzleHttp\Client;

class MyWebController
{
    protected $oauthSession;

    private function setUpOAuth()
    {
        $httpClient = new Client(['base_uri' => 'http://myoauth2provider.example.com']);
        $redirector = new MyRedirector($this);

        // your application's settings for the OAuth2 provider
        $clientConfig = new ClientConfig([
            'client_id' => 'client_123',
            'redirect_uri' => 'http://myapp.example.com/',
        ]);

        // the OAuth2 provider's settings
        $serverConfig = new ServerConfig([
            'authorization_endpoint' => '/oauth/authorize',
            'token_endpoint' => '/oauth/token',
        ]);

        $this->oauthSession = new AuthorizationCodeSession(
            $httpClient,
            $redirector,
            $clientConfig,
            $serverConfig
        );

        $scope = new Scope(['USER_READ', 'DATA_READ_WRITE']);
        $this->oauthSession->setScope($scope);
    }
}
```

When you are ready to connect to the service secured with OAuth2, you will need
to authorize your user.

```php
$this->oauthSession->authorize();
```

The OAuth2 server will redirect the user back to your application
with the user's token, at the
[url specified when the client was registered with the OAuth2 provider](http://tools.ietf.org/html/rfc6749#section-2),
or at the `redirect_uri` optionally specified in the `ClientConfig`. Your
application should handle that request as follows:

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;

class MyWebController
{
    // this is the action which handles the redirect from the OAuth2 server
    public function actionReceiveAuthorizationResponseFromOAuth()
    {
        $authorizationResponse = new AuthorizationResponse($_GET);
        $this->oauthSession->handleAuthorizationResponse($authorizationResponse);
    }
}
```

At this point you can access the service being provided, via a fresh Guzzle
client.

```php
$handler = \GuzzleHttp\HandlerStack::create()
$stackHandler->before('http_errors', function ($callable) {
    return new \EasyBib\Guzzle\BearerAuthMiddleware($callable, $this->oauthSession);
});
$resourceHttpClient = new \GuzzleHttp\Client([
    'base_uri' => 'http://coolresources.example.com',
    'handler' => $handler,
]);
$response = $resourceHttpClient->get('/some/resource');
// etc.
```

A subscriber has been added to the client which
will add the necessary header to subsequent requests:

```
GET /some/resource HTTP/1.1
Authorization: Bearer token_foo_bar_baz
```

## Token expiration and invalidation

This client will automatically handle token renewal when communicating with
OAuth2 servers which provide a `refresh_token`.

In the event that the resource server you are communicating with invalidates
the token, e.g. the user logs out, you will need to handle that condition
within your application, as
[the OAuth2 standard does not specify behavior of the resource server in that case](http://tools.ietf.org/html/rfc6749#section-1.5).

When that situation is detected within your app, call the `authorize` method
again:

```php
$this->oauthSession->authorize();
```

## State

For protection against cross-site request forgeries, the OAuth2 standard
[recommends using a random `state` parameter](http://tools.ietf.org/html/rfc6749#section-4.1.1)
with authorization code requests.
Google Developers has
[more information about this](https://developers.google.com/accounts/docs/OAuth2Login#createxsrftoken).

`AuthorizationCodeSession` uses state. To omit state, use
`StatelessAuthorizationCodeSession`:

```php
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\StatelessAuthorizationCodeSession;

$this->oauthSession = new StatelessAuthorizationCodeSession(
    $httpClient,
    $redirector,
    $clientConfig,
    $serverConfig
);
```
