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
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

class Client extends AbstractClient
{
    public const VERSION = "43.0";

    public const BASE_PATH = "services/data/v".self::VERSION."/";

    public function __construct(GuzzleClient $client, SerializerInterface $serializer)
    {
        $this->client     = $client;
        $this->serializer = $serializer;
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
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'sobjects/'.$sObjectType)
        );

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
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function describe(string $sObjectType): DescribeSObject
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'sobjects/'.$sObjectType.'/describe')
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            DescribeSObject::class,
            'json'
        );
    }

    /**
     * @return GlobalDescribe
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function describeGlobal(): GlobalDescribe
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'sobjects/')
        );

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
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $sObjectType, string $id, array $fields = ['Id']): SObject
    {
        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'sobjects/'.$sObjectType.'/'.$id.'?'.
                http_build_query(
                    [
                        'fields' => implode(",", $fields),
                    ]
                )
            )
        );

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

        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'sobjects/'.$sObjectType.'/updated/?'.
                http_build_query(
                    [
                        'start' => $start->format(\DateTime::ISO8601),
                        'end'   => $end->format(\DateTime::ISO8601),
                    ]
                )
            )
        );

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

        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'sobjects/'.$sObjectType.'/deleted/?'.
                http_build_query(
                    [
                        'start' => $start->format(\DateTime::ISO8601),
                        'end'   => $end->format(\DateTime::ISO8601),
                    ]
                )
            )
        );

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
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function persist(string $SObjectType, SObject $SObject): bool
    {
        $method        = null !== $SObject->Id ? 'patch' : 'post';
        $id            = $SObject->Id;
        $url           = self::BASE_PATH.'sobjects/'.$SObjectType.(null !== $id ? '/'.$id : '');
        $SObject->Id   = null;
        $SObject->Type = null;

        $request = new Request(
            $method,
            $url,
            [],
            $this->serializer->serialize($SObject, 'json')
        );

        $response = $this->send($request, $method === "patch" ? 204 : 201);

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

        $this->send(
            new Request(
                "DELETE",
                self::BASE_PATH.'sobjects/'.$SObjectType.'/'.$SObject->Id
            ),
            204
        );
    }

    /**
     * @param $query
     *
     * @return QueryResult
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function query($query): QueryResult
    {
        if ($query instanceof QueryResult) {
            if ($query->isDone()) {
                return $query;
            }

            $url = $query->getNextRecordsUrl();
        } else {
            $url = self::BASE_PATH.'query/?'.
                http_build_query(
                    [
                        'q' => $query,
                    ]
                );
        }

        $response = $this->send(
            new Request("GET", $url)
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            QueryResult::class,
            'json'
        );
    }

    /**
     * @param $query
     *
     * @return QueryResult
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryAll($query): QueryResult
    {
        if ($query instanceof QueryResult) {
            return $this->query($query);
        }

        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'queryAll/?'.
                http_build_query(
                    [
                        'q' => $query,
                    ]
                )
            )
        );

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
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search(string $query): SearchResult
    {
        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'search/?'.
                http_build_query(
                    [
                        'q' => $query,
                    ]
                )
            )
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            SearchResult::class,
            'json'
        );
    }
}
