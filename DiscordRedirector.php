<?php

require_once("vendor/autoload.php");
use EasyBib\OAuth2\Client\AuthorizationCodeGrant\RedirectorInterface;

class DiscordRedirector implements RedirectorInterface
{
    private $controller;

    public function __construct()
    {
    }

    public function redirect($url)
    {
        return header("Location: $url");
    }
}
