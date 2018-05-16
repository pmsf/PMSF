<?php

namespace EasyBib\Tests\Guzzle\Exception;

use EasyBib\Guzzle\Exception\BearerErrorResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class BearerErrorResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateBearerReason()
    {
        $statusCode = 403;
        $headers = [
            'WWW-Authenticate' => 'Bearer realm="myprotectedresource", '
                .'error="insufficient_scope", error_description="Insufficient scope for this resource scopes"',
        ];
        $reason = 'foo-bar';
        $request = new Request('GET', '/sample');
        $response = new Response($statusCode, $headers, null, '1.1', $reason);
        $exception = BearerErrorResponseException::create($request, $response);

        $this->assertInstanceOf(BearerErrorResponseException::class, $exception);
        $this->assertSame('insufficient_scope', $exception->getBearerReason());
    }
}
