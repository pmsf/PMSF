<?php

namespace EasyBib\OAuth2\Client;

use EasyBib\OAuth2\Client\TokenResponse\TokenResponse;

interface TokenRequestInterface
{
    /**
     * @return TokenResponse
     */
    public function send();
}
