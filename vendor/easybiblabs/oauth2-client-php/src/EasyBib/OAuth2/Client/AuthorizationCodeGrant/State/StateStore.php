<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant\State;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization\AuthorizationResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class StateStore
{
    const KEY_STATE = 'oauth/state';

    /**
     * This is a persistent store for state data, which does not necessarily
     * strictly correspond to a user's PHP session
     *
     * @var Session
     */
    private $session;

    /**
     * @var StateGeneratorInterface
     */
    private $stateGenerator;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->stateGenerator = new SimpleStateGenerator();
    }

    /**
     * @return string
     */
    public function getState()
    {
        if ($state = $this->get(self::KEY_STATE)) {
            return $state;
        }

        $state = $this->stateGenerator->generate();
        $this->session->set(self::KEY_STATE, $state);

        return $state;
    }

    /**
     * @param AuthorizationResponse $response
     * @return bool
     * @throws \LogicException
     */
    public function validateResponse(AuthorizationResponse $response)
    {
        if (!$this->isInitiated()) {
            throw new \LogicException('State not initiated');
        }

        if (empty($response->getParams()['state'])) {
            return false;
        }

        return $response->getParams()['state'] == $this->getState();
    }

    /**
     * @param StateGeneratorInterface $stateGenerator
     */
    public function setStateGenerator(StateGeneratorInterface $stateGenerator)
    {
        $this->stateGenerator = $stateGenerator;
    }

    /**
     * @return bool
     */
    private function isInitiated()
    {
        return (bool) $this->get(self::KEY_STATE);
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
