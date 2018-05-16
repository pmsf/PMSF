<?php

namespace EasyBib\Tests\Mocks\OAuth2\Client;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\RedirectorInterface;

/**
 * This simulates redirects. It can be detected and caught within test harnesses,
 * as well as outputting a message and exiting in command-line test scripts.
 *
 * Class ExceptionMockRedirector
 * @package EasyBib\Tests\Mocks\Api\Client\Session
 */
class ExceptionMockRedirector implements RedirectorInterface
{
    /**
     * @param string $url
     * @throws MockRedirectException
     */
    public function redirect($url)
    {
        throw new MockRedirectException('Redirecting to ' . $url);
    }
}
