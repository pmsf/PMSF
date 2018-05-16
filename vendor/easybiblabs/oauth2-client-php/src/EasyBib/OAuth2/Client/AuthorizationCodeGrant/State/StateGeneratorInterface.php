<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant\State;

interface StateGeneratorInterface
{
    /**
     * @return string
     */
    public function generate();
}
