<?php

namespace EasyBib\Tests\OAuth2\Client\AuthorizationCodeGrant\State;

use EasyBib\OAuth2\Client\AuthorizationCodeGrant\State\SimpleStateGenerator;

class SimpleStateGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleStateGenerator
     */
    private $generator;

    public function setUp()
    {
        parent::setUp();

        $this->generator = new SimpleStateGenerator(
            SimpleStateGenerator::DEFAULT_STRING_LENGTH
        );
    }

    public function testGenerate()
    {
        $this->assertInternalType('string', $this->generator->generate());

        $this->assertEquals(
            SimpleStateGenerator::DEFAULT_STRING_LENGTH,
            strlen($this->generator->generate())
        );
    }
}
