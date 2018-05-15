<?php

namespace EasyBib\OAuth2\Client;

use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class TokenStore
{
    const KEY_ACCESS_TOKEN  = 'oauth/access_token';
    const KEY_REFRESH_TOKEN = 'oauth/refresh_token';
    const KEY_EXPIRES_AT    = 'oauth/expires_at';

    /**
     * Treat token as expired if fewer than this number of seconds remains
     * until the expires_in point is reached
     */
    const EXPIRATION_WIGGLE_ROOM = 10;

    /**
     * This is a persistent store for token data, which does not necessarily
     * strictly correspond to a user's PHP session
     *
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if ($this->isExpired()) {
            return null;
        }

        return $this->get(self::KEY_ACCESS_TOKEN);
    }

    public function reset()
    {
        $this->session->remove(self::KEY_ACCESS_TOKEN);
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->get(self::KEY_REFRESH_TOKEN);
    }

    /**
     * @return bool
     */
    public function isRefreshable()
    {
        return $this->get(self::KEY_ACCESS_TOKEN) && $this->get(self::KEY_REFRESH_TOKEN);
    }

    /**
     * @param \EasyBib\OAuth2\Client\TokenResponse\TokenResponse $tokenResponse
     */
    public function updateFromTokenResponse(TokenResponse $tokenResponse)
    {
        // don't use replace(), as that resets first and then sets
        $this->session->set(self::KEY_ACCESS_TOKEN, $tokenResponse->getToken());
        $this->session->set(self::KEY_REFRESH_TOKEN, $tokenResponse->getRefreshToken());
        $this->session->set(self::KEY_EXPIRES_AT, $this->expirationTimeFor($tokenResponse));
    }

    /**
     * @param \EasyBib\OAuth2\Client\TokenResponse\TokenResponse $tokenResponse
     * @return int
     */
    private function expirationTimeFor(TokenResponse $tokenResponse)
    {
        return time() + $tokenResponse->getExpiresIn();
    }

    /**
     * @return bool
     */
    private function isExpired()
    {
        $expiresAt = $this->get(self::KEY_EXPIRES_AT);

        return $expiresAt && $expiresAt < time() + self::EXPIRATION_WIGGLE_ROOM;
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function get($name)
    {
        return $this->session->get($name);
    }
}
