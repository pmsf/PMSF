<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant\State;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\StateStore;
use EasyBib\Tests\OAuth2\Client\Mock\AuthorizationCodeGrant\State\MockStateGenerator;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class StateStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var StateStore
     */
    private $stateStore;

    public function setUp()
    {
        parent::setUp();

        $this->session = new Session(new MockArraySessionStorage());
        $this->stateStore = new StateStore($this->session);
    }

    public function testGetState()
    {
        $stateValue = 'ABC123';
        $this->session->set(StateStore::KEY_STATE, $stateValue);

        $this->assertEquals($stateValue, $this->stateStore->getState());
    }

    public function testGetStateWithGeneration()
    {
        $state = $this->stateStore->getState();

        $this->assertInternalType('string', $state);
        $this->assertNotEmpty($state);
        $this->assertEquals($state, $this->session->get(StateStore::KEY_STATE));
        $this->assertEquals($state, $this->stateStore->getState());
    }

    public function testValidateResponseWhereMatches()
    {
        $stateValue = 'ABC123';
        $this->session->set(StateStore::KEY_STATE, $stateValue);
        $response = $this->getAuthorizationResponse($stateValue);

        $this->assertTrue($this->stateStore->validateResponse($response));
    }

    public function testValidateResponseWhereDoesNotMatch()
    {
        $this->session->set(StateStore::KEY_STATE, 'ABC123');
        $response = $this->getAuthorizationResponse('DEF456');

        $this->assertFalse($this->stateStore->validateResponse($response));
    }

    public function testValidateResponseWhereNotInitialized()
    {
        $response = $this->getAuthorizationResponse('ABC123');

        $this->setExpectedException('\LogicException');
        $this->stateStore->validateResponse($response);
    }

    public function testSetStateGenerator()
    {
        $value = 'hooha';
        $this->stateStore->setStateGenerator(new MockStateGenerator($value));
        $this->assertEquals($value, $this->stateStore->getState());
    }

    /**
     * @param string $state
     * @return AuthorizationResponse
     */
    private function getAuthorizationResponse($state)
    {
        return new AuthorizationResponse([
            'code'  => 'XYZ987',
            'state' => $state,
        ]);
    }
}
