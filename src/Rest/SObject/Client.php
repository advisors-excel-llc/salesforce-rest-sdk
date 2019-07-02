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
use AE\SalesforceRestSdk\Model\Rest\GenericEvents;
use AE\SalesforceRestSdk\Model\Rest\Metadata\BasicInfo;
use AE\SalesforceRestSdk\Model\Rest\Metadata\DescribeSObject;
use AE\SalesforceRestSdk\Model\Rest\Metadata\GlobalDescribe;
use AE\SalesforceRestSdk\Model\Rest\QueryResult;
use AE\SalesforceRestSdk\Model\Rest\SearchResult;
use AE\SalesforceRestSdk\Model\Rest\StreamingChannelResponse;
use AE\SalesforceRestSdk\Model\Rest\UpdatedResponse;
use AE\SalesforceRestSdk\Model\SObject;
use GuzzleHttp\Psr7\Request;
use AE\SalesforceRestSdk\Rest\Client as BaseClient;

class Client
{
    /**
     * @var BaseClient
     */
    private $client;

    public function __construct(BaseClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $sObjectType
     *
     * @return BasicInfo
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info(string $sObjectType): BasicInfo
    {
        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request("GET", "$basePath/sobjects/$sObjectType")
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            BasicInfo::class,
            'json'
        )
            ;
    }

    /**
     * @param string $sObjectType
     *
     * @return DescribeSObject
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function describe(string $sObjectType): DescribeSObject
    {
        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request("GET", "$basePath/sobjects/$sObjectType/describe")
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            DescribeSObject::class,
            'json'
        )
            ;
    }

    /**
     * @return GlobalDescribe
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function describeGlobal(): GlobalDescribe
    {
        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request("GET", "$basePath/sobjects/")
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            GlobalDescribe::class,
            'json'
        )
            ;
    }

    /**
     * @param string $sObjectType
     * @param string $id
     * @param array $fields
     *
     * @return SObject
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $sObjectType, string $id, array $fields = ['Id']): SObject
    {
        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request(
                "GET",
                "$basePath/sobjects/$sObjectType/$id?".
                http_build_query(
                    [
                        'fields' => implode(",", $fields),
                    ]
                )
            )
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            SObject::class,
            'json'
        )
            ;
    }

    /**
     * @param string $sObjectType
     * @param \DateTime $start
     * @param \DateTime|null $end
     *
     * @return UpdatedResponse
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUpdated(string $sObjectType, \DateTime $start, \DateTime $end = null): UpdatedResponse
    {
        if (null === $end) {
            $end = new \DateTime();
        }

        $start->setTimezone(new \DateTimeZone("UTC"));
        $end->setTimezone(new \DateTimeZone("UTC"));

        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request(
                "GET",
                "$basePath/sobjects/$sObjectType/updated/?".
                http_build_query(
                    [
                        'start' => $start->format('Y-m-d\TH:i:sP'),
                        'end'   => $end->format('Y-m-d\TH:i:sP'),
                    ],
                    null,
                    '&',
                    PHP_QUERY_RFC3986
                )
            )
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            UpdatedResponse::class,
            'json'
        )
            ;
    }

    /**
     * @param string $sObjectType
     * @param \DateTime $start
     * @param \DateTime|null $end
     *
     * @return DeletedResponse
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDeleted(string $sObjectType, \DateTime $start, \DateTime $end = null): DeletedResponse
    {
        if (null === $end) {
            $end = new \DateTime();
        }

        $start->setTimezone(new \DateTimeZone("UTC"));
        $end->setTimezone(new \DateTimeZone("UTC"));

        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request(
                "GET",
                "$basePath/sobjects/$sObjectType/deleted/?".
                http_build_query(
                    [
                        'start' => $start->format('Y-m-d\TH:i:sP'),
                        'end'   => $end->format('Y-m-d\TH:i:sP'),
                    ],
                    null,
                    '&',
                    PHP_QUERY_RFC3986
                )
            )
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            DeletedResponse::class,
            'json'
        )
            ;
    }

    /**
     * @param string $SObjectType
     * @param SObject $SObject
     *
     * @return bool
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function persist(string $SObjectType, SObject $SObject): bool
    {
        $basePath    = $this->getBasePath();
        $method      = null !== $SObject->Id ? 'patch' : 'post';
        $id          = $SObject->Id;
        $url         = "$basePath/sobjects/$SObjectType".(null !== $id ? '/'.$id : '');
        $SObject->Id = null;
        $serializer  = $this->client->getSerializer();
        $request     = new Request(
            $method,
            $url,
            [],
            $serializer->serialize($SObject, 'json')
        );
        $response    = $this->client->send($request, $method === "patch" ? 204 : 201);

        if ($method === 'post') {
            /** @var CreateResponse $body */
            $body = $serializer->deserialize(
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

        return true;
    }

    /**
     * @param string $SObjectType
     * @param SObject $SObject
     *
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function remove(string $SObjectType, SObject $SObject)
    {
        if (null === $SObject->Id) {
            throw new \RuntimeException("The SObject provided does not have an ID set.");
        }

        $basePath = $this->getBasePath();

        $this->client->send(
            new Request(
                "DELETE",
                "$basePath/sobjects/$SObjectType/{$SObject->Id}"
            ),
            204
        );
    }

    /**
     * @param QueryResult|null $query
     * @param int $batchSize
     *
     * @return QueryResult
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($query, int $batchSize = 2000): QueryResult
    {
        if ($query instanceof QueryResult) {
            if ($query->isDone()) {
                return $query;
            }

            $url = $query->getNextRecordsUrl();
        } else {
            $basePath = $this->getBasePath();
            $url      = "$basePath/query/?".
                http_build_query(
                    [
                        'q' => $query,
                    ]
                );
        }

        $response = $this->client->send(
            new Request("GET", $url, ['Sforce-Query-Options' => $batchSize])
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            QueryResult::class,
            'json'
        );
    }

    /**
     * @param $query
     * @param int $batchSize
     *
     * @return QueryResult
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryAll($query, int $batchSize = 2000): QueryResult
    {
        if ($query instanceof QueryResult) {
            return $this->query($query);
        }

        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request(
                "GET",
                "$basePath/queryAll/?".
                http_build_query(
                    [
                        'q' => $query,
                    ]
                ),
                [
                    'Sforce-Query-Options' => "batchSize=$batchSize",
                ]
            )
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            QueryResult::class,
            'json'
        );
    }

    /**
     * @param string $query
     * @param int $batchSize
     *
     * @return SearchResult
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(string $query, int $batchSize = 2000): SearchResult
    {
        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request(
                "GET",
                "$basePath/search/?".
                http_build_query(
                    [
                        'q' => $query,
                    ]
                ),
                ['Sforce-Query-Options' => "batchSize=$batchSize"]
            )
        );

        $body = (string)$response->getBody();

        return $this->client->getSerializer()->deserialize(
            $body,
            SearchResult::class,
            'json'
        );
    }

    /**
     * @param string $channelId
     * @param GenericEvents $events
     *
     * @return StreamingChannelResponse
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendGenericEvents(string $channelId, GenericEvents $events)
    {
        $basePath   = $this->getBasePath();
        $serializer = $this->client->getSerializer();
        $response   = $this->client->send(
            new Request(
                "POST",
                "$basePath/sobjects/StreamingChannel/$channelId/push",
                [],
                $serializer->serialize($events, 'json')
            )
        );

        /** @var StreamingChannelResponse $body */
        $body = $serializer->deserialize(
            $response->getBody(),
            StreamingChannelResponse::class,
            'json'
        );

        return $body;
    }

    /**
     * @param string $channelId
     *
     * @return array|SObject[]
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStreamingChannelSubscribers(string $channelId): array
    {
        $basePath = $this->getBasePath();
        $response = $this->client->send(
            new Request(
                "GET",
                "$basePath/sobjects/StreamingChannel/$channelId/push"
            )
        );

        /** @var array|SObject[] $body */
        $body = $this->client->getSerializer()->deserialize(
            $response->getBody(),
            "array<".SObject::class.">",
            'json'
        );

        return $body;
    }

    public function getBasePath(): string
    {
        return "services/data/v{$this->client->getVersion()}";
    }
}
