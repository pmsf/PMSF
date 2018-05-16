<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\TokenStore;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class Given
{
    /**
     * @param string $token
     * @param Scope $scope
     * @param MockHandler $mockHandler
     */
    public function iAmReadyToRespondToATokenRequest($token, Scope $scope, MockHandler $mockHandler)
    {
        $mockHandler->append($this->rawTokenResponse($token, $scope));
    }

    /**
     * @param string $token
     * @param Scope $scope
     * @return Response
     */
    public function rawTokenResponse($token, Scope $scope)
    {
        $params = [
                'access_token' => $token,
                'expires_in' => 3600,
                'token_type' => 'bearer',
                'refresh_token' => 'refresh_XYZ987',
            ] + $scope->getQuerystringParams();

        $tokenData = json_encode($params);

        return new Response(200, [], $tokenData);
    }

    /**
     * @param string $token
     * @param Session $session
     */
    public function iHaveATokenInSession($token, Session $session)
    {
        $session->set(TokenStore::KEY_ACCESS_TOKEN, $token);
    }

    public function iHaveRandomOtherDataInMySession(Session $session, array $data)
    {
        $session->replace($data);
    }

    /**
     * @param Session $session
     */
    public function myTokenIsExpired(Session $session)
    {
        $session->set(TokenStore::KEY_EXPIRES_AT, time() - 100);
    }

    /**
     * @param Session $session
     * @param int $after
     */
    public function myTokenExpiresLater(Session $session, $after = 100)
    {
        $session->set(TokenStore::KEY_EXPIRES_AT, time() + $after);
    }

    /**
     * @param $refreshToken
     * @param Session $session
     */
    public function iHaveARefreshToken($refreshToken, Session $session)
    {
        $session->set(TokenStore::KEY_REFRESH_TOKEN, $refreshToken);
    }
}
