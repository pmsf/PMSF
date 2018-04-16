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
    // does something which eventually calls header() to redirect user
    return header("Location: $url");
    }
}
