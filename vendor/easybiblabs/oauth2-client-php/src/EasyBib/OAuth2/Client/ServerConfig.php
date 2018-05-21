<?php

namespace EasyBib\OAuth2\Client;

class ServerConfig
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private static $validParams = [
        'token_endpoint',
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
     * @throws InvalidServerConfigException
     */
    private static function validate(array $params)
    {
        $validator = new ArrayValidator(self::$validParams, self::$validParams);

        if (!$validator->validate($params)) {
            throw new InvalidServerConfigException();
        }
    }
}
