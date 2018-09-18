<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 5:51 PM
 */

namespace AE\SalesforceRestSdk\Rest;

use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param ResponseInterface $response
     * @param int $expectedStatusCode
     *
     * @throws \RuntimeException
     */
    protected function throwErrorIfInvalidResponseCode(ResponseInterface $response, int $expectedStatusCode = 200)
    {
        if ($response->getStatusCode() !== $expectedStatusCode) {
            $body = (string)$response->getBody();
            if (strlen($body) > 0) {
                $errors = $this->serializer->deserialize($body, 'array', 'json');
                throw new \RuntimeException("{$errors[0]['errorCode']}: {$errors[0]['message']}");
            } else {
                throw new \RuntimeException(
                    "Received Status Code {$response->getStatusCode()}, expected $expectedStatusCode"
                );
            }
        }
    }
}
