<?php

namespace EasyBib\OAuth2\Client;

interface TokenRequestFactoryInterface
{
    /**
     * @return TokenRequestInterface
     */
    public function create();
}
