<?php

namespace EasyBib\Tests\OAuth2\Client;

use EasyBib\OAuth2\Client\Scope;
use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;
use EasyBib\OAuth2\Client\TokenStore;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class TokenStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Given
     */
    private $given;

    /**
     * @var Session
     */
    private $tokenSession;

    /**
     * @var TokenStore
     */
    private $tokenStore;

    public function setUp()
    {
        $this->given = new Given();
        $this->tokenSession = new Session(new MockArraySessionStorage());
        $this->tokenStore = new TokenStore($this->tokenSession);
    }

    public function dataForIsRefreshable()
    {
        $token = 'ABC123';
        $refreshToken = 'XYZ987';

        return [
            [[TokenStore::KEY_ACCESS_TOKEN => $token], false],
            [[TokenStore::KEY_ACCESS_TOKEN => $token, TokenStore::KEY_REFRESH_TOKEN => $refreshToken], true],
        ];
    }

    public function testGetToken()
    {
        $token = 'jimbob';
        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->given->myTokenExpiresLater($this->tokenSession);
        $this->assertEquals($token, $this->tokenStore->getToken());
    }

    public function testGetTokenWhenExpired()
    {
        $token = 'jimbob';
        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->given->myTokenIsExpired($this->tokenSession);

        $this->assertNull($this->tokenStore->getToken());
    }

    public function testReset()
    {
        $token = 'jimbob';
        $this->given->iHaveATokenInSession($token, $this->tokenSession);
        $this->given->myTokenExpiresLater($this->tokenSession);
        $this->given->iHaveRandomOtherDataInMySession($this->tokenSession, ['foo' => 'bar']);

        $this->tokenStore->reset();

        $this->assertNull($this->tokenStore->getToken());
        $this->assertEquals('bar', $this->tokenSession->get('foo'));
    }

    public function testUpdateFromTokenResponse()
    {
        $this->given->iHaveRandomOtherDataInMySession($this->tokenSession, ['foo' => 'bar']);

        $rawTokenResponse = $this->given->rawTokenResponse('123', new Scope(['read']));
        $tokenResponse = new TokenResponse($rawTokenResponse);

        $this->tokenStore->updateFromTokenResponse($tokenResponse);
        $this->assertEquals('bar', $this->tokenSession->get('foo'));
    }

    /**
     * @dataProvider dataForIsRefreshable
     * @param array $params
     * @param $expectedValue
     */
    public function testIsRefreshable(array $params, $expectedValue)
    {
        $this->tokenSession->replace($params);
        $this->assertSame($expectedValue, $this->tokenStore->isRefreshable());
    }
}
