<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 2:20 PM
 */

namespace AE\SalesforceRestSdk\Rest\SObject;

use AE\SalesforceRestSdk\Model\Rest\CreateResponse;
use AE\SalesforceRestSdk\Model\Rest\DeletedResponse;
use AE\SalesforceRestSdk\Model\Rest\Metadata\BasicInfo;
use AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject;
use AE\SalesforceRestSdk\Model\Rest\Metadata\GlobalDescribe;
use AE\SalesforceRestSdk\Model\Rest\QueryResult;
use AE\SalesforceRestSdk\Model\Rest\SearchResult;
use AE\SalesforceRestSdk\Model\Rest\UpdatedResponse;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use GuzzleHttp\Client as GuzzleClient;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

class Client extends AbstractClient
{
    public const VERSION = "43.0";

    public const BASE_URI = "services/data/v".self::VERSION."/";

    public function __construct(GuzzleClient $client, SerializerInterface $serializer)
    {
        $this->client     = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param string $sObjectType
     *
     * @return BasicInfo
     */
    public function info(string $sObjectType): BasicInfo
    {
        $response = $this->client->get(
            self::BASE_URI.'sobjects/'.$sObjectType
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            BasicInfo::class,
            'json'
        );
    }

    /**
     * @param string $sObjectType
     *
     * @return DescribeSObject
     */
    public function describe(string $sObjectType): DescribeSObject
    {
        $response = $this->client->get(
            self::BASE_URI.'sobjects/'.$sObjectType.'/describe'
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

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
            self::BASE_URI.'sobjects/'
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

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
            self::BASE_URI.'sobjects/'.$sObjectType.'/'.$id,
            [
                'query' => [
                    'fields' => implode(",", $fields),
                ],
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            SObject::class,
            'json'
        );
    }

    /**
     * @param string $sObjectType
     * @param \DateTime $start
     * @param \DateTime|null $end
     *
     * @return UpdatedResponse
     */
    public function getUpdated(string $sObjectType, \DateTime $start, \DateTime $end = null): UpdatedResponse
    {
        if (null === $end) {
            $end = new \DateTime();
        }

        $start->setTimezone(new \DateTimeZone("UTC"));
        $end->setTimezone(new \DateTimeZone("UTC"));

        $response = $this->client->get(
            self::BASE_URI.'sobjects/'.$sObjectType,
            [
                'query' => [
                    'start' => $start->format(\DateTime::ISO8601),
                    'end'   => $end->format(\DateTime::ISO8601),
                ],
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            UpdatedResponse::class,
            'json'
        );
    }

    /**
     * @param string $sObjectType
     * @param \DateTime $start
     * @param \DateTime|null $end
     *
     * @return DeletedResponse
     */
    public function getDeleted(string $sObjectType, \DateTime $start, \DateTime $end = null): DeletedResponse
    {
        if (null === $end) {
            $end = new \DateTime();
        }

        $start->setTimezone(new \DateTimeZone("UTC"));
        $end->setTimezone(new \DateTimeZone("UTC"));

        $response = $this->client->get(
            self::BASE_URI.'sobjects/'.$sObjectType,
            [
                'query' => [
                    'start' => $start->format(\DateTime::ISO8601),
                    'end'   => $end->format(\DateTime::ISO8601),
                ],
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            DeletedResponse::class,
            'json'
        );
    }

    /**
     * @param string $SObjectType
     * @param SObject $SObject
     *
     * @return bool
     */
    public function persist(string $SObjectType, SObject $SObject): bool
    {
        $method        = null !== $SObject->Id ? 'patch' : 'post';
        $id            = $SObject->Id;
        $url           = self::BASE_URI.'sobjects/'.$SObjectType.(null !== $id ? '/'.$id : '');
        $SObject->Id   = null;
        $SObject->Type = null;

        /** @var ResponseInterface $response */
        $response = $this->client->$method(
            $url,
            [
                'body' => $this->serializer->serialize($SObject, 'json'),
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response, $method === "patch" ? 204 : 201);

        if ($method === 'post') {
            /** @var CreateResponse $body */
            $body = $this->serializer->deserialize(
                $response->getBody(),
                CreateResponse::class,
                'json'
            );

            if ($body->isSuccess()) {
                $SObject->Id = $body->getId();
            } else {
                $error = '';

                foreach ($body->getErrors() as $err) {
                    $fields = implode(",", $err->getFields());
                    $error  .= "{$err->getStatusCode()}: {$err->getMessage()} (Fields: $fields)".PHP_EOL;
                }

                throw new \RuntimeException($error);
            }
        } else {
            $SObject->Id = $id;
        }

        $SObject->Type = $SObjectType;

        return true;
    }

    public function remove(string $SObjectType, SObject $SObject)
    {
        if (null === $SObject->Id) {
            throw new \RuntimeException("The SObject provided does not have an ID set.");
        }

        $response = $this->client->delete(
            self::BASE_URI.'sobjects/'.$SObjectType.'/'.$SObject->Id
        );

        $this->throwErrorIfInvalidResponseCode($response, 204);
    }

    /**
     * @param string|QueryResult $query
     *
     * @return QueryResult
     */
    public function query($query): QueryResult
    {
        if ($query instanceof QueryResult) {
            if ($query->isDone()) {
                return $query;
            }

            $response = $this->client->get(
                $query->getNextRecordsUrl()
            );
        } else {
            $response = $this->client->get(
                self::BASE_URI.'query/',
                [
                    'query' => [
                        'q' => $query,
                    ],
                ]
            );
        }

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            QueryResult::class,
            'json'
        );
    }

    public function queryAll($query): QueryResult
    {
        if ($query instanceof QueryResult) {
            return $this->query($query);
        }

        $response = $this->client->get(
            self::BASE_URI.'queryAll/',
            [
                'query' => [
                    'q' => $query,
                ],
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            QueryResult::class,
            'json'
        );
    }

    /**
     * @param string $query
     *
     * @return SearchResult
     */
    public function search(string $query): SearchResult
    {
        $response = $this->client->get(
            self::BASE_URI.'search/',
            [
                'query' => [
                    'q' => $query,
                ],
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            SearchResult::class,
            'json'
        );
    }
}
