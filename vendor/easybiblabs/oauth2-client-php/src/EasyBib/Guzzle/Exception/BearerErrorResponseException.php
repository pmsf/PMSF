<?php

namespace EasyBib\Guzzle\Exception;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BearerErrorResponseException
 * @link https://github.com/fkooman/guzzle-bearer-auth-plugin
 */
class BearerErrorResponseException extends RequestException
{
    /**
     * @var string
     */
    private $bearerReason;

    /**
     * @return string
     */
    public function getBearerReason()
    {
        return $this->bearerReason;
    }

    /**
     * @param string $bearerReason
     */
    public function setBearerReason($bearerReason)
    {
        $this->bearerReason = $bearerReason;
    }

    /**
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Exception $previous
     * @param array $ctx
     * @return BearerErrorResponseException
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $previous = null,
        array $ctx = []
    ) {
        unset($previous, $ctx);
        $label = 'Bearer error response';
        $bearerReason = self::headerToReason($response->getHeader('WWW-Authenticate'));
        $message = $label . PHP_EOL . implode(PHP_EOL, [
            '[status code] ' . $response->getStatusCode(),
            '[reason phrase] ' . $response->getReasonPhrase(),
            '[bearer reason] ' . $bearerReason,
            '[url] ' . $request->getUri(),
        ]);

        $exception = new static($message, $request, $response);
        $exception->setBearerReason($bearerReason);

        return $exception;
    }

    /**
     * @param string[]|false $headers
     * @return string
     */
    public static function headerToReason($headers)
    {
        if (!empty($headers)) {
            $parsedHeaders = \GuzzleHttp\Psr7\parse_header($headers);
            foreach ($parsedHeaders as $value) {
                if (isset($value['error'])) {
                    return $value['error'];
                }
            }
        }

        return null;
    }
}
