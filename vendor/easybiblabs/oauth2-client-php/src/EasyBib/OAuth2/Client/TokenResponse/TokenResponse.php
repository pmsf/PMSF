<?php

namespace EasyBib\OAuth2\Client\TokenResponse;

use EasyBib\OAuth2\Client\ArrayValidator;
use Psr\Http\Message\ResponseInterface;

class TokenResponse
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private static $requiredParams = [
        'access_token',
        'token_type',
    ];

    /**
     * @var array
     */
    private static $requiredErrorParams = [
        'error',
    ];

    /**
     * @var array
     */
    private static $permittedErrorParams = [
        'error',
        'error_description',
        'error_uri',
    ];

    /**
     * @param ResponseInterface $httpResponse
     * @throws InvalidTokenResponseException
     */
    public function __construct(ResponseInterface $httpResponse)
    {
        $this->validateHttp($httpResponse);

        $this->params = $this->extractParams($httpResponse);

        if (!$this->isSuccess() && !$this->isError()) {
            throw new InvalidTokenResponseException();
        }
    }

    /**
     * @throws TokenRequestErrorException
     * @return string
     */
    public function getToken()
    {
        if ($this->isError()) {
            throw new TokenRequestErrorException($this->params['error']);
        }

        return $this->params['access_token'];
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->paramOrNull('refresh_token');
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->paramOrNull('expires_in');
    }

    /**
     * @return bool
     */
    private function isSuccess()
    {
        $validator = new ArrayValidator(self::$requiredParams);
        return $validator->validate($this->params);
    }

    /**
     * @return bool
     */
    private function isError()
    {
        $validator = new ArrayValidator(
            self::$requiredErrorParams,
            self::$permittedErrorParams
        );

        return $validator->validate($this->params);
    }

    /**
     * @param ResponseInterface $httpResponse
     * @throws UnexpectedHttpErrorException
     */
    private function validateHttp(ResponseInterface $httpResponse)
    {
        if ($httpResponse->getStatusCode() >= 400) {
            throw new UnexpectedHttpErrorException($httpResponse->getStatusCode());
        }
    }

    /**
     * @param ResponseInterface $httpResponse
     * @return array
     * @throws InvalidTokenResponseException
     */
    private function extractParams(ResponseInterface $httpResponse)
    {
        $params = json_decode($httpResponse->getBody(), true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidTokenResponseException(json_last_error());
        }

        return $params;
    }

    /**
     * @param string $name
     * @return string
     */
    private function paramOrNull($name)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        return null;
    }
}
