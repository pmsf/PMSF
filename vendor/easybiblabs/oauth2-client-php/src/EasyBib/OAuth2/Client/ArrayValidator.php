<?php

namespace EasyBib\OAuth2\Client;

class ArrayValidator
{
    /**
     * @var array
     */
    private $requiredKeys;

    /**
     * @var array
     */
    private $permittedKeys;

    /**
     * @param array $requiredKeys
     * @param array $permittedKeys An optional whitelist for array keys
     */
    public function __construct(array $requiredKeys, array $permittedKeys = null)
    {
        $this->requiredKeys = $requiredKeys;
        $this->permittedKeys = $permittedKeys;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function validate(array $params)
    {
        $missingKeys = array_diff($this->requiredKeys, array_keys($params));

        if ($missingKeys) {
            return false;
        }

        if (!$this->permittedKeys) {
            return true;
        }

        $unexpectedKeys = array_diff(array_keys($params), $this->permittedKeys);

        return !$unexpectedKeys;
    }
}
