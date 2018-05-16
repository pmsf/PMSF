<?php

namespace EasyBib\OAuth2\Client;

class Scope
{
    /**
     * @var string[]
     */
    private $scopes;

    /**
     * @param string[] $scopes
     */
    public function __construct(array $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @return array
     */
    public function getQuerystringParams()
    {
        if (!$this->scopes) {
            return [];
        }

        return ['scope' => implode(' ', $this->scopes)];
    }
}
