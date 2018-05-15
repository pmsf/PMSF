<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant\Authorization;

use EasyBib\OAuth2\Client\ArrayValidator;

class AuthorizationResponse
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private static $requiredSuccessParams = [
        'code',
    ];

    private static $permittedSuccessParams = [
        'code',
        'state',
    ];

    /**
     * @var array
     */
    private static $requiredErrorParams = [
        'error',
    ];

    private static $permittedErrorParams = [
        'error',
        'error_description',
        'error_uri',
        'state',
    ];

    /**
     * @param array $params
     * @throws InvalidAuthorizationResponseException
     */
    public function __construct(array $params)
    {
        $this->params = $params;

        if (!$this->isSuccess() && !$this->isError()) {
            $message = sprintf(
                'Invalid authorization response params: %s',
                json_encode($params)
            );

            throw new InvalidAuthorizationResponseException($message);
        }
    }

    /**
     * @throws AuthorizationErrorException
     * @return string
     */
    public function getCode()
    {
        if ($this->isError()) {
            throw new AuthorizationErrorException($this->params['error']);
        }

        return $this->params['code'];
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return bool
     */
    private function isSuccess()
    {
        $validator = new ArrayValidator(
            self::$requiredSuccessParams,
            self::$permittedSuccessParams
        );

        return $validator->validate($this->params);
    }

    private function isError()
    {
        $validator = new ArrayValidator(
            self::$requiredErrorParams,
            self::$permittedErrorParams
        );

        return $validator->validate($this->params);
    }
}
