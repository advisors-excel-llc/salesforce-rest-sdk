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
use AE\SalesforceRestSdk\Model\Rest\CountResult;
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

    /**
     * @var array
     */
    protected $callOptions = [];

    public function __construct(AuthProviderInterface $provider, string $version = "44.0", ?string $appName = null)
    {
        if (null !== $appName) {
            $this->callOptions = ['client' => $appName];
        }

        $this->version         = $version;
        $this->authProvider    = $provider;
        $this->client          = $this->createHttpClient();
        $this->serializer      = $this->createSerializer();
        $this->compositeClient = new CompositeClient(
            $this->client,
            $this->serializer,
            $this->authProvider,
            $this->version
        );
        $this->sObjectClient   = new SObject\Client(
            $this->client,
            $this->serializer,
            $this->authProvider,
            $this->version
        );
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
        $request  = new Request("GET", '/services/data/v'.$this->getVersion().'/limits/');
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

    /**
     * @param array $sObjectTypes
     *
     * @return CountResult
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function count(array $sObjectTypes = []): CountResult
    {
        $url = '/services/data/v'.$this->getVersion().'/limits/recordCount';

        if (!empty($sObjectTypes)) {
            $url .= '?sObjects='.implode(',', $sObjectTypes);
        }

        $request  = new Request("GET", $url);
        $response = $this->send($request);
        $body     = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            CountResult::class,
            'json'
        );
    }

    /**
     * @return GuzzleClient
     * @throws SessionExpiredOrInvalidException
     */
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

        $headers = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        $client = new GuzzleClient(
            [
                'base_uri' => $url,
                'handler'  => $stack,
                'headers'  => $headers,
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

    protected function appendSforceCallOptions(RequestInterface $request): RequestInterface
    {
        $callOptions = array_reduce(
            array_keys($this->callOptions),
            function (array $carry, $key) {
                $value = $this->callOptions[$key];
                if (strlen($value) > 0) {
                    $carry[] = "$key=$value";
                }

                return $carry;
            },
            []
        );

        if (!empty($callOptions)) {
            return $request->withAddedHeader('Sforce-Call-Options', implode(" ", $callOptions));
        }

        return $request;
    }

    protected function send(RequestInterface $request, $expectedStatusCode = 200)
    {
        return parent::send($this->appendSforceCallOptions($request), $expectedStatusCode);
    }

    /**
     * @param array $options
     *
     * @return Client
     */
    public function setCallOptions(array $options): self
    {
        $this->callOptions = $options;

        return $this;
    }

    /**
     * @param $name
     * @param string $value
     *
     * @return Client
     */
    public function setCallOption($name, string $value): self
    {
        $this->callOptions[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     *
     * @return Client
     */
    public function removeCallOption($name): self
    {
        unset($this->callOptions[$name]);

        return $this;
    }
}
