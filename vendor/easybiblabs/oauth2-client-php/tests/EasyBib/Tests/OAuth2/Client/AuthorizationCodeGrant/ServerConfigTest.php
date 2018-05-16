<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\ServerConfig;

class ServerConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getValidParamSets()
    {
        return [
            [
                [
                    'authorization_endpoint' => '/oauth/authorize',
                    'token_endpoint' => '/oauth/token'
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidParamSets()
    {
        return [
            [[]],
            [['joe']],
            [['authorization_endpoint' => '/foo']],
            [['token_endpoint' => '/foo']],
        ];
    }

    /**
     * @dataProvider getValidParamSets
     * @param array $params
     */
    public function testValidParams(array $params)
    {
        new ServerConfig($params);
    }

    /**
     * @dataProvider getInvalidParamSets
     * @param array $params
     */
    public function testInvalidParams(array $params)
    {
        $exceptionClass = '\EasyBib\OAuth2\Client\InvalidServerConfigException';
        $this->setExpectedException($exceptionClass);

        new ServerConfig($params);
    }
}
