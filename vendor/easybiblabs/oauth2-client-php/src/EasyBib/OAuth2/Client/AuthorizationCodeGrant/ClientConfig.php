<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\ArrayValidator;
use EasyBib\OAuth2\Client\InvalidClientConfigException;

class ClientConfig
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private static $requiredParams = [
        'client_id',
    ];

    /**
     * @var array
     */
    private static $permittedParams = [
        'client_id',
        'redirect_uri',
        'client_secret',
        'resource',
    ];

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        self::validate($params);
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @throws \EasyBib\OAuth2\Client\InvalidClientConfigException
     */
    private static function validate(array $params)
    {
        $validator = new ArrayValidator(self::$requiredParams, self::$permittedParams);

        if (!$validator->validate($params)) {
            throw new InvalidClientConfigException();
        }
    }
}
