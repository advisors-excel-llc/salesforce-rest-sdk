<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 5:51 PM
 */

namespace AE\SalesforceRestSdk\Rest;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\RequestInterface;
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
     * @var AuthProviderInterface
     */
    protected $authProvider;

    /**
     * @var string
     */
    protected $version = "44.0";

    /**
     * @param RequestInterface $request
     * @param int $expectedStatusCode
     *
     * @return mixed|ResponseInterface
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function send(RequestInterface $request, $expectedStatusCode = 200)
    {
        $response = $this->client->send($request);

        try {
            $this->throwErrorIfInvalidResponseCode($response, $expectedStatusCode);
        } catch (SessionExpiredOrInvalidException $e) {
            $this->authProvider->reauthorize();
            return $this->send($request, $expectedStatusCode);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param int $expectedStatusCode
     *
     * @throws \RuntimeException|SessionExpiredOrInvalidException
     */
    protected function throwErrorIfInvalidResponseCode(ResponseInterface $response, int $expectedStatusCode = 200)
    {
        if ($response->getStatusCode() !== $expectedStatusCode) {
            $body = (string)$response->getBody();
            if (strlen($body) > 0) {
                $errors = $this->serializer->deserialize($body, 'array', 'json');

                if (401 === $response->getStatusCode()) {
                    throw new SessionExpiredOrInvalidException($errors[0]['message'], $errors[0]['errorCode']);
                } else {
                    $error = array_key_exists(0, $errors) ? $errors[0] : $errors;
                    if (array_key_exists('errorCode', $error)) {
                        throw new \RuntimeException("{$error['errorCode']}: {$error['message']}");
                    } elseif (array_key_exists('exceptionCode', $error)) {
                        throw new \RuntimeException("{$error['exceptionCode']}: {$error['exceptionMessage']}");
                    } else {
                        throw new \RuntimeException(
                            "Received Status Code {$response->getStatusCode()}, expected $expectedStatusCode"
                        );
                    }
                }
            } else {
                throw new \RuntimeException(
                    "Received Status Code {$response->getStatusCode()}, expected $expectedStatusCode"
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
