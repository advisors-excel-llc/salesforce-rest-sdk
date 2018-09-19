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
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
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
                'body' => $this->serializer->serialize($request, 'json')
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string) $response->getBody();
        $compositeResponse = $this->serializer->deserialize(
            $body,
            CompositeResponse::class,
            'json'
        );

        // TODO: process responses to generate correct body
    }

    public function batch()
    {
        // TODO: Implement Batch
    }
}
