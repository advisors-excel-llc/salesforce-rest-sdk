<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 11:34 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeCollection;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeTreeResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\Query\QuerySubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\BasicInfoSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection\CompositeCollectionSubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\CreateSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DescribeGlobalSubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DescribeSubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetDeletedSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetUpdatedSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\CreateResponse;
use AE\SalesforceRestSdk\Model\Rest\DeletedResponse;
use AE\SalesforceRestSdk\Model\Rest\Metadata\BasicInfo;
use AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject;
use AE\SalesforceRestSdk\Model\Rest\Metadata\GlobalDescribe;
use AE\SalesforceRestSdk\Model\Rest\QueryResult;
use AE\SalesforceRestSdk\Model\Rest\UpdatedResponse;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class CompositeClient extends AbstractClient
{
    public const VERSION = '43.0';

    public const BASE_PATH = '/services/data/v'.self::VERSION.'/composite';

    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client     = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param CollectionRequestInterface $request
     *
     * @return CollectionResponse[]
     */
    public function create(CollectionRequestInterface $request): array
    {
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->client->post(
            self::BASE_PATH.'/sobjects',
            [
                'body' => $requestBody,
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

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
     * @return array|CompositeSObject[]
     */
    public function read(string $sObjectType, array $ids, array $fields = ['id']): array
    {
        $response = $this->client->get(
            self::BASE_PATH.'/sobjects/'.$sObjectType,
            [
                'query' => [
                    'ids'    => implode(",", $ids),
                    'fields' => implode(",", $fields),
                ],
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        return $this->serializer->deserialize(
            $response->getBody(),
            'array<'.CompositeSObject::class.'>',
            'json'
        );
    }

    /**
     * @param CollectionRequestInterface $request
     *
     * @return CollectionResponse[]
     */
    public function update(CollectionRequestInterface $request): array
    {
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->client->patch(
            self::BASE_PATH.'/sobjects',
            [
                'body' => $requestBody,
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

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
     * @return CollectionResponse[]
     */
    public function delete(CollectionRequestInterface $request): array
    {
        $ids = [];

        foreach ($request->getRecords() as $record) {
            if (null !== $record->id) {
                $ids[] = $record->id;
            }
        }

        $response = $this->client->delete(
            self::BASE_PATH.'/sobjects',
            [
                'query' => [
                    'allOrNone' => $request->isAllOrNone() ? "true" : "false",
                    'ids'       => implode(",", $ids),
                ],
            ]
        );

        return $this->serializer->deserialize(
            $response->getBody(),
            'array<'.CollectionResponse::class.'>',
            'json'
        );
    }

    public function sendCompositeRequest(CompositeRequest $request)
    {
        $response = $this->client->post(
            self::BASE_PATH,
            [
                'body' => $this->serializer->serialize($request, 'json'),
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();
        /** @var CompositeResponse $compositeResponse */
        $compositeResponse = $this->serializer->deserialize(
            $body,
            CompositeResponse::class,
            'json'
        );

        foreach ($compositeResponse->getCompositeResponse() as $index => $result) {
            if (null !== $result->getBody()) {
                // For every response, there had to be a request
                $req  = $request->findSubRequestByReferenceId($result->getReferenceId());
                $type = $this->getDeserializationType($req);
                $body = $this->serializer->serialize($result->getBody(), 'json');

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

    protected function getDeserializationType(SubRequest $request): string
    {
        if ($request instanceof QuerySubRequest) {
            return QueryResult::class;
        } elseif ($request instanceof BasicInfoSubRequest) {
            return BasicInfo::class;
        } elseif ($request instanceof DescribeGlobalSubRequestInterface) {
            return GlobalDescribe::class;
        } elseif ($request instanceof DescribeSubRequestInterface) {
            return DescribeSObject::class;
        } elseif ($request instanceof CreateSubRequest) {
            return CreateResponse::class;
        } elseif ($request instanceof GetUpdatedSubRequest) {
            return UpdatedResponse::class;
        } elseif ($request instanceof GetDeletedSubRequest) {
            return DeletedResponse::class;
        } elseif ($request instanceof GetSubRequest) {
            return SObject::class;
        } elseif ($request instanceof CompositeCollectionSubRequestInterface) {
            return 'array<'.CollectionResponse::class.'>';
        }

        return 'array';
    }

    /**
     * @param string $sObjectType
     * @param CompositeCollection $collection
     *
     * @return CompositeTreeResponse
     */
    public function tree(string $sObjectType, CompositeCollection $collection): CompositeTreeResponse
    {
        $response = $this->client->post(
            self::BASE_PATH.'/tree/'.$sObjectType,
            [
                'body' => $this->serializer->serialize($collection, 'json'),
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response, 201);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            CompositeTreeResponse::class,
            'json'
        );
    }

    public function batch()
    {
        // TODO: Implement Batch
    }
}
