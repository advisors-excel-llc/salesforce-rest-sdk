<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 11:34 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class CompositeClient extends AbstractClient
{
    public const VERSION = 'v43.0';

    public const BASE_PATH = '/services/data/'.self::VERSION.'/composite/sobjects';

    public function __construct(Client $client, SerializerInterface $serializer)
    {
        $this->client     = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param CompositeRequestInterface $request
     *
     * @return CompositeResponse[]
     */
    public function create(CompositeRequestInterface $request): array
    {
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->client->post(
            self::BASE_PATH,
            [
                'body' => $requestBody,
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            'array<'.CompositeResponse::class.'>',
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
            self::BASE_PATH.'/'.$sObjectType,
            [
                'query' => [
                    'ids'    => implode($ids),
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
     * @param CompositeRequestInterface $request
     *
     * @return CompositeResponse[]
     */
    public function update(CompositeRequestInterface $request): array
    {
        $requestBody = $this->serializer->serialize($request, 'json');
        $response    = $this->client->patch(
            self::BASE_PATH,
            [
                'body' => $requestBody,
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            'array<'.CompositeResponse::class.'>',
            'json'
        );
    }

    /**
     * @param CompositeRequestInterface $request
     *
     * @return CompositeResponse[]
     */
    public function delete(CompositeRequestInterface $request): array
    {
        $ids = [];

        foreach ($request->getRecords() as $record) {
            if (null !== $record->id) {
                $ids[] = $record->id;
            }
        }

        $response = $this->client->delete(
            self::BASE_PATH,
            [
                'query' => [
                    'allOrNone' => $request->isAllOrNone() ? "true" : "false",
                    'ids'       => implode(",", $ids),
                ],
            ]
        );

        return $this->serializer->deserialize(
            $response->getBody(),
            'array<'.CompositeResponse::class.'>',
            'json'
        );
    }

    public function batch()
    {
        // TODO: Implement Batch
    }
}
