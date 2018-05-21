# JSON Web Token (JWT) Grant

The flow of JSON Web Token Grants is very similar to that of
[Client Credentials grants](client-credentials-grant.md),
but the token request can contain much more encoded data.

As of the current version, only a small subset of the JWT specification is
supported.

Please refer to [the tests](../tests/EasyBib/Tests/OAuth2/Client/JsonWebTokenGrant/)
in order to implement this grant type.
