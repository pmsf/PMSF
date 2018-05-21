# Usage Documentation

## Token store

In order to keep your user logged in, you will need a persistent token store.
This is implemented using the Session component of the Symfony HTTP Foundation
package. By default, this client uses native PHP sessions, but you can use
[that framework](http://symfony.com/doc/current/components/http_foundation/sessions.html)
to implement whatever backend makes sense in the context of your
application. Just use `setTokenStore()` on your `AbstractSession` instance before
you start using it, in order to substitute an instance of your custom class.

## Implementing this client in your app

The implementations of `AbstractSession`, together with `TokenRequest`, are the
heart of this library. They wrap a Guzzle `Client`, which communicates with the
OAuth2 server. They also allows you to attach any number of Guzzle clients to the
session, which will thereafter have access to the token necessary to make requests
against resource servers.

This is accomplished by creating a second Guzzle `Client` for use with your
resource provider, and attaching it to your Session:

```php
$handler = \GuzzleHttp\HandlerStack::create()
$stackHandler->before('http_errors', function ($callable) use ($session) {
    return new \EasyBib\Guzzle\BearerAuthMiddleware($callable, $session);
});
$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://cool-api.example.org',
    'handler' => $handler,
]);
```

## Token grants

* [Authorization Code](authorization-code-grant.md)
* [Client Credentials](client-credentials-grant.md)
* [JSON Web Token](json-web-token-grant.md)

## TokenRequest factories

The [Abstract Factory pattern](http://en.wikipedia.org/wiki/Abstract_factory_pattern)
is used in conjunction with `SimpleSession` to create tokens for Authorization
Code and Json Web Token grants, in order to to insulate request-specific
concerns from the `SimpleSession` implementation.

## Error handling

OAuth2 specifies anticipated errors that can be returned from the server. This
client represents two types in exceptions: `AuthorizationErrorException` and
`TokenRequestErrorException`. You can find the documentation
[here](http://tools.ietf.org/html/rfc6749#section-4.1.2.1) and
[here](http://tools.ietf.org/html/rfc6749#section-5.2), respectively.

In case the OAuth2 server returns an invalid (i.e. unexpected) response to an
authorization or token request, the client will throw an
`InvalidAuthorizationResponseException` or `InvalidTokenResponseException`,
respectively.
