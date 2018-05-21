<?php

namespace EasyBib\OAuth2\Client\AuthorizationCodeGrant\State;

class SimpleStateGenerator implements StateGeneratorInterface
{
    const DEFAULT_STRING_LENGTH = 30;

    /** @var int */
    private $stringLength;

    /**
     * @param int $stringLength
     */
    public function __construct($stringLength = self::DEFAULT_STRING_LENGTH)
    {
        $this->stringLength = $stringLength;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $chars = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $numChars = count($chars);

        $string = '';

        for ($i = 0; $i < $this->stringLength; $i++) {
            $string .= $chars[rand(0, $numChars-1)];
        }

        return $string;
    }
}
