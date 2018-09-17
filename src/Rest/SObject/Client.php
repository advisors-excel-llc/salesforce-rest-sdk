<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 2:20 PM
 */

namespace AE\SalesforceRestSdk\Rest\SObject;

use AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject;
use AE\SalesforceRestSdk\Model\Rest\Metadata\GlobalDescribe;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use GuzzleHttp\Client as GuzzleClient;
use JMS\Serializer\SerializerInterface;

class Client extends AbstractClient
{
    public const VERSION = "43.0";

    public const BASE_URI = "services/data/v".self::VERSION."/sobjects/";

    public function __construct(GuzzleClient $client, SerializerInterface $serializer)
    {
        $this->client     = $client;
        $this->serializer = $serializer;
    }

    public function info(string $sObjectType)
    {

    }

    /**
     * @param string $sObjectType
     *
     * @return DescribeSObject
     */
    public function describe(string $sObjectType): DescribeSObject
    {
        $response = $this->client->get(
            self::BASE_URI.$sObjectType
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string) $response->getBody();

        return $this->serializer->deserialize(
            $body,
            DescribeSObject::class,
            'json'
        );
    }

    /**
     * @return GlobalDescribe
     */
    public function describeGlobal(): GlobalDescribe
    {
        $response = $this->client->get(
            self::BASE_URI
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string) $response->getBody();

        return $this->serializer->deserialize(
            $body,
            GlobalDescribe::class,
            'json'
        );
    }

    /**
     * @param string $sObjectType
     * @param string $id
     * @param array $fields
     *
     * @return SObject
     */
    public function get(string $sObjectType, string $id, array $fields = ['Id']): SObject
    {
        $response = $this->client->get(
            self::BASE_URI.$sObjectType.'/'.$id,
            [
                'query' => [
                    'fields' => implode(",", $fields)
                ]
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string) $response->getBody();

        return $this->serializer->deserialize(
            $body,
            SObject::class,
            'json'
        );
    }

    public function getUpdated(string $sObjectType, \DateTimeInterface $start, \DateTimeInterface $end = null)
    {

    }

    public function getDeleted(string $sObjectType, \DateTimeInterface $start, \DateTimeInterface $end = null)
    {

    }

    public function persist(SObject $SObject)
    {

    }

    public function remove(SObject $SObject)
    {

    }

    public function query()
    {

    }

    public function queryAll()
    {

    }

    public function search()
    {

    }
}
