<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 11:57 AM
 */

namespace AE\SalesforceRestSdk\Rest;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException;
use AE\SalesforceRestSdk\Model\Rest\Limits;
use AE\SalesforceRestSdk\Rest\Composite\CompositeClient;
use AE\SalesforceRestSdk\Serializer\CompositeSObjectHandler;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\RequestInterface;

class Client extends AbstractClient
{
    public const VERSION = "43.0";

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var CompositeClient
     */
    protected $compositeClient;

    /**
     * @var \AE\SalesforceRestSdk\Rest\SObject\Client
     */
    protected $sObjectClient;

    public function __construct(AuthProviderInterface $provider)
    {
        $this->authProvider    = $provider;
        $this->client          = $this->createHttpClient();
        $this->serializer      = $this->createSerializer();
        $this->compositeClient = new CompositeClient($this->client, $this->serializer);
        $this->sObjectClient   = new SObject\Client($this->client, $this->serializer);
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return AuthProviderInterface
     */
    public function getAuthProvider(): AuthProviderInterface
    {
        return $this->authProvider;
    }

    /**
     * @return GuzzleClient
     */
    public function getHttpClient(): GuzzleClient
    {
        return $this->client;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @return CompositeClient
     */
    public function getCompositeClient(): CompositeClient
    {
        return $this->compositeClient;
    }

    /**
     * @return SObject\Client
     */
    public function getSObjectClient(): SObject\Client
    {
        return $this->sObjectClient;
    }

    /**
     * @return Limits
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function limits(): Limits
    {
        $request = new Request("GET", '/services/data/v'.self::VERSION.'/limits/');
        $response = $this->send($request);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            Limits::class,
            'json'
        );
    }

    public function apex(
        string $method,
        string $path,
        $payload = null,
        string $responseType = 'array',
        array $headers = [],
        $expectedResponseCode = 200
    ) {
        $headers['Content-Type'] = 'application/json';
        $headers['Accept']       = 'application/json';

        $body    = null !== $payload ? $this->serializer->serialize($payload, 'json') : null;
        $request = new Request($method, '/services/apexrest'.$path, $headers, $body);

        $response = $this->client->request($request);

        try {
            $this->throwErrorIfInvalidResponseCode($response, $expectedResponseCode);
        } catch (SessionExpiredOrInvalidException $e) {
            return $this->apex(
                $method,
                $path,
                $payload,
                $responseType,
                $headers,
                $expectedResponseCode
            );
        }

        $resBody = (string)$response->getBody();

        if (null !== $resBody) {
            return $this->serializer->deserialize(
                $resBody,
                $responseType,
                'json'
            );
        }

        return null;
    }

    protected function createHttpClient(): GuzzleClient
    {
        $stack = new HandlerStack();
        $stack->setHandler(\GuzzleHttp\choose_handler());
        $stack->push(
            Middleware::mapRequest(
                function (RequestInterface $request) {
                    return $this->authorize($request);
                }
            )
        );

        $url = $this->authProvider->getInstanceUrl();

        // If the instance URL isn't set, try and get it from the auth provider
        if (null === $url) {
            $this->authProvider->authorize();
            $url = $this->authProvider->getInstanceUrl();
        }

        $client = new GuzzleClient(
            [
                'base_uri' => $url,
                'handler'  => $stack,
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ]
        );

        return $client;
    }

    protected function createSerializer(): SerializerInterface
    {
        $builder = SerializerBuilder::create();
        $builder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $builder->addDefaultHandlers()
                ->addDefaultListeners()
                ->addDefaultDeserializationVisitors()
                ->addDefaultSerializationVisitors()
                ->configureHandlers(
                    function (HandlerRegistry $handler) {
                        $handler->registerSubscribingHandler(new SObjectHandler());
                        $handler->registerSubscribingHandler(new CompositeSObjectHandler());
                    }
                )
        ;

        return $builder->build();
    }

    protected function authorize(RequestInterface $request): RequestInterface
    {
        return $request->withAddedHeader('Authorization', $this->authProvider->authorize());
    }
}
