<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 11:57 AM
 */

namespace AE\SalesforceRestSdk\Rest;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\Model\Rest\Limits;
use AE\SalesforceRestSdk\Rest\Composite\CompositeClient;
use AE\SalesforceRestSdk\Serializer\CompositeSObjectHandler;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
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
     * @var AuthProviderInterface
     */
    protected $authProvider;

    /**
     * @var CompositeClient
     */
    protected $compositeClient;

    /**
     * @var \AE\SalesforceRestSdk\Rest\SObject\Client
     */
    protected $sObjectClient;

    public function __construct(string $baseUrl, AuthProviderInterface $provider)
    {
        $this->baseUrl         = $baseUrl;
        $this->authProvider    = $provider;
        $this->client          = $this->createHttpClient($baseUrl);
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
     * @throws \RuntimeException
     * @return Limits
     */
    public function limits(): Limits
    {
        $response = $this->client->get(
            '/services/data/v'.self::VERSION.'/limits/'
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            Limits::class,
            'json'
        );
    }

    protected function createHttpClient(string $url): GuzzleClient
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
