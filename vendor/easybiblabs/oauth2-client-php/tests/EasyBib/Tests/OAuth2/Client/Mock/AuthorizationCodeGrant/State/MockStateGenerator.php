<?php

namespace EasyBib\Tests\OAuth2\Client\Mock\AuthorizationCodeGrant\State;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\StateGeneratorInterface;

class MockStateGenerator implements StateGeneratorInterface
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function generate()
    {
        return $this->value;
    }
}
