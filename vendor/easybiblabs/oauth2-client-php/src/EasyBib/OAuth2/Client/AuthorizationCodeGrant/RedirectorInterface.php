<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

interface RedirectorInterface
{
    /**
     * @param $url
     */
    public function redirect($url);
}
