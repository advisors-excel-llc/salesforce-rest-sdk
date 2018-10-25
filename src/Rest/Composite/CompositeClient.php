<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 11:34 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

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
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\SerializerInterface;

class CompositeClient extends AbstractClient
{
    public const VERSION = '44.0';

    public const BASE_PATH = '/services/data/v'.self::VERSION.'/composite';

    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client     = $client;
        $this->serializer = $serializer;
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
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->send(
            new Request("POST", self::BASE_PATH.'/sobjects', [], $requestBody)
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
        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'/sobjects/'.$sObjectType.'?'.
                http_build_query(
                    [
                        'ids'    => implode(",", $ids),
                        'fields' => implode(",", $fields),
                    ]
                )
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
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->send(
            new Request(
                "PATCH",
                self::BASE_PATH.'/sobjects',
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

        $response = $this->send(
            new Request(
                "DELETE",
                self::BASE_PATH.'/sobjects?'.
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
                self::BASE_PATH,
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
        $response = $this->send(
            new Request(
                "POST",
                self::BASE_PATH.'/tree/'.$sObjectType,
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
        $response = $this->send(
            new Request(
                "POST",
                self::BASE_PATH.'/batch',
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
}
