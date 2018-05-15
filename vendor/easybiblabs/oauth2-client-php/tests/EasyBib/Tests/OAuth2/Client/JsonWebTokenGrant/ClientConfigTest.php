<?php

namespace EasyBib\Tests\OAuth2\Client\JsonWebTokenGrant;

use EasyBib\OAuth2\Client\JsonWebTokenGrant\ClientConfig;

class ClientConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getValidParamSets()
    {
        return [
            [
                [
                    'client_id' => 'ABC123',
                    'client_secret' => 'XYZ987',
                    'subject' => 'MNO456',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidParamSets()
    {
        $validSet = $this->getValidParamSets()[0][0];

        $invalidSets = [];

        foreach (array_keys($validSet) as $key) {
            $set = $validSet;
            unset($set[$key]);
            $invalidSets[] = [$set];
        }

        return $invalidSets;
    }

    /**
     * @dataProvider getInvalidParamSets
     * @param array $params
     */
    public function testConstructorValidates(array $params)
    {
        $exceptionClass = '\EasyBib\OAuth2\Client\InvalidClientConfigException';
        $this->setExpectedException($exceptionClass);

        new ClientConfig($params);
    }
}
