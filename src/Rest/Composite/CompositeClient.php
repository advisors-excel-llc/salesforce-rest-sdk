<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 11:34 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\BatchRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\BatchResult;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeCollection;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeTreeResponse;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use AE\SalesforceRestSdk\Rest\Composite\Builder\BatchRequestBuilder;
use AE\SalesforceRestSdk\Rest\Composite\Builder\CompositeRequestBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\SerializerInterface;

class CompositeClient extends AbstractClient
{
    // Derived from protocol + max subdomain size + "my.salesforce.com"
    // @See https://help.salesforce.com/articleView?id=faq_domain_name_is_there_a_limit.htm&type=5
    public const MAX_HOSTNAME_SIZE = 59;

    // URI Length maxes out at 16,088 for Salesforce
    // @See https://salesforce.stackexchange.com/questions/195449/what-is-the-longest-uri-that-salesforce-will-accept-through-the-rest-api/195450
    public const MAX_URI_LENGTH = 16088;

    public function __construct(
        Client $client,
        SerializerInterface $serializer,
        AuthProviderInterface $provider,
        string $version = "44.0"
    ) {
        $this->client       = $client;
        $this->serializer   = $serializer;
        $this->authProvider = $provider;
        $this->version      = $version;
    }

    public function compositeRequestBuilder(): CompositeRequestBuilder
    {
        return new CompositeRequestBuilder($this->version);
    }

    public function batchRequestBuilder(): BatchRequestBuilder
    {
        return new BatchRequestBuilder($this->version);
    }

    /**
     * @param CollectionRequestInterface $request
     *
     * @return array
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(CollectionRequestInterface $request): array
    {
        $basePath    = $this->getBasePath();
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->send(
            new Request("POST", "$basePath/sobjects", [], $requestBody)
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            'array<'.CollectionResponse::class.'>',
            'json'
        );
    }

    /**
     * @param string $sObjectType
     * @param array $ids
     * @param array $fields
     *
     * @return array
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function read(string $sObjectType, array $ids, array $fields = ['id']): array
    {
        if (empty($ids) || empty($fields)) {
            return [];
        }

        $basePath  = $this->getBasePath();
        $url       = "$basePath/sobjects/$sObjectType";
        $method    = "GET";
        $body      = null;
        $query     = http_build_query(
            [
                'ids'    => implode(",", $ids),
                'fields' => implode(",", $fields),
            ]
        );
        $uriLength = self::MAX_HOSTNAME_SIZE + strlen($url."?$query");

        if ($uriLength > self::MAX_URI_LENGTH) {
            $method = "POST";
            $body   = $this->serializer->serialize(
                [
                    'ids'    => $ids,
                    'fields' => $fields,
                ],
                'json'
            );
        } else {
            $url .= "?$query";
        }

        $response = $this->send(
            new Request(
                $method,
                $url,
                [],
                $body
            )
        );

        return $this->serializer->deserialize(
            $response->getBody(),
            'array<'.CompositeSObject::class.'>',
            'json'
        );
    }

    /**
     * @param CollectionRequestInterface $request
     *
     * @return array
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(CollectionRequestInterface $request): array
    {
        $basePath    = $this->getBasePath();
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->send(
            new Request(
                "PATCH",
                "$basePath/sobjects",
                [],
                $requestBody
            )
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            'array<'.CollectionResponse::class.'>',
            'json'
        );
    }

    /**
     * @param CollectionRequestInterface $request
     *
     * @return array
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(CollectionRequestInterface $request): array
    {
        $ids = [];

        foreach ($request->getRecords() as $record) {
            if (null !== $record->id) {
                $ids[] = $record->id;
            }
        }

        $basePath = $this->getBasePath();
        $response = $this->send(
            new Request(
                "DELETE",
                "$basePath/sobjects?".
                http_build_query(
                    [
                        'allOrNone' => $request->isAllOrNone() ? "true" : "false",
                        'ids'       => implode(",", $ids),
                    ]
                )
            )
        );

        return $this->serializer->deserialize(
            $response->getBody(),
            'array<'.CollectionResponse::class.'>',
            'json'
        );
    }

    /**
     * @param CompositeRequest $request
     *
     * @return CompositeResponse
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendCompositeRequest(CompositeRequest $request)
    {
        $response = $this->send(
            new Request(
                "POST",
                $this->getBasePath(),
                [],
                $this->serializer->serialize($request, 'json')
            )
        );

        $body = (string)$response->getBody();
        /** @var CompositeResponse $compositeResponse */
        $compositeResponse = $this->serializer->deserialize(
            $body,
            CompositeResponse::class,
            'json'
        );

        foreach ($compositeResponse->getCompositeResponse() as $index => $result) {
            // For every response, there had to be a request
            $req    = $request->findSubRequestByReferenceId($result->getReferenceId());
            $type   = $req->getResultClass();
            $output = $result->getBody();

            if (null !== $type && null !== $output) {
                $body = $this->serializer->serialize($output, 'json');

                $result->setBody(
                    $this->serializer->deserialize(
                        $body,
                        $type,
                        'json'
                    )
                );
            }
        }

        return $compositeResponse;
    }

    /**
     * @param string $sObjectType
     * @param CompositeCollection $collection
     *
     * @return CompositeTreeResponse
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tree(string $sObjectType, CompositeCollection $collection): CompositeTreeResponse
    {
        $basePath = $this->getBasePath();
        $response = $this->send(
            new Request(
                "POST",
                "$basePath/tree/$sObjectType",
                [],
                $this->serializer->serialize($collection, 'json')
            ),
            201
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            CompositeTreeResponse::class,
            'json'
        );
    }

    /**
     * @param BatchRequest $request
     *
     * @return BatchResult
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function batch(BatchRequest $request)
    {
        $basePath = $this->getBasePath();
        $response = $this->send(
            new Request(
                "POST",
                "$basePath/batch",
                [],
                $this->serializer->serialize($request, 'json')
            )
        );

        $body = (string)$response->getBody();
        /** @var BatchResult $batchResult */
        $batchResult = $this->serializer->deserialize(
            $body,
            BatchResult::class,
            'json'
        );

        $requests = $request->getBatchRequests();
        foreach ($batchResult->getResults() as $i => $result) {
            $req    = $requests[$i];
            $type   = $req->getResultClass();
            $output = $result->getResult();

            if (null !== $type && null !== $output) {
                $input = $this->serializer->serialize($output, 'json');

                $result->setResult(
                    $this->serializer->deserialize(
                        $input,
                        $type,
                        'json'
                    )
                );
            }
        }

        return $batchResult;
    }

    public function getBasePath(): string
    {
        return "/services/data/v{$this->getVersion()}/composite";
    }
}
