<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 5:52 PM
 */

namespace AE\SalesforceRestSdk\Bulk;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException;
use AE\SalesforceRestSdk\Psr7\CsvStream;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use AE\SalesforceRestSdk\Serializer\CompositeSObjectHandler;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\stream_for;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class Client
 *
 * @package AE\SalesforceRestSdk\Bulk
 */
class Client extends AbstractClient
{
    /**
     *
     */
    public const VERSION = "44.0";

    /**
     *
     */
    public const BASE_PATH = "/services/async/".self::VERSION."/job";

    /**
     * Client constructor.
     *
     * @param AuthProviderInterface $authProvider
     *
     * @throws SessionExpiredOrInvalidException
     */
    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
        $this->client       = $this->createHttpClient();
        $this->serializer   = $this->createSerializer();
    }

    /**
     * @return \GuzzleHttp\Client
     * @throws SessionExpiredOrInvalidException
     */
    protected function createHttpClient(): \GuzzleHttp\Client
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

        if (null === $url) {
            $this->authProvider->authorize();
            $url = $this->authProvider->getInstanceUrl();
        }

        return new \GuzzleHttp\Client(
            [
                'base_uri' => $url,
                'handler'  => $stack,
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
            ]
        );
    }

    /**
     * @return SerializerInterface
     */
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

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface
     * @throws SessionExpiredOrInvalidException
     */
    protected function authorize(RequestInterface $request): RequestInterface
    {
        $token = explode(' ', $this->authProvider->authorize());

        return $request->withAddedHeader('X-SFDC-Session', array_pop($token));
    }

    /**
     * @param string $sObjectType
     * @param string $operation
     * @param string $contentType
     * @param string $externalIdField
     * @param string $concurrencyMode
     *
     * @return JobInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createJob(
        string $sObjectType,
        string $operation,
        string $contentType,
        ?string $externalIdField = null,
        string $concurrencyMode = "Parallel"
    ): JobInfo {
        $jobInfo = new JobInfo();

        $ops = [
            JobInfo::INSERT,
            JobInfo::UPDATE,
            JobInfo::UPSERT,
            JobInfo::DELETE,
            JobInfo::HARD_DELETE,
            JobInfo::QUERY,
            JobInfo::QUERY_ALL
        ];
        if (!in_array($operation, $ops)) {
            throw new \RuntimeException("Operation is not one of: ".implode(', ', $ops));
        }

        if (JobInfo::UPSERT === $operation && null === $externalIdField) {
            throw new \RuntimeException("Upsert operations require the External Id Field be specified.");
        }

        $jobInfo->setObject($sObjectType)
                ->setOperation($operation)
                ->setContentType($contentType)
                ->setConcurrencyMode($concurrencyMode)
                ->setExternalIdFieldName($externalIdField)
        ;

        $response = $this->send(
            new Request(
                "POST",
                self::BASE_PATH,
                [],
                $this->serializer->serialize($jobInfo, 'json')
            ),
            201
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            'json'
        );
    }

    /**
     * @param JobInfo $job
     * @param $batch
     *
     * @return BatchInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addBatch(JobInfo $job, $batch): BatchInfo
    {
        $contentType = "application/json";

        if (JobInfo::TYPE_CSV === $job->getContentType()) {
            $contentType = "text/csv";
        } elseif (JobInfo::TYPE_XML === $job->getContentType()) {
            $contentType = "application/xml";
        }

        if (!is_string($batch)) {
            $batch = $this->serializer->serialize($batch, 'json');
        }

        $response = $this->send(
            new Request(
                "POST",
                self::BASE_PATH.'/'.$job->getId().'/batch',
                [
                    'Content-Type' => $contentType,
                ],
                $batch
            ),
            201
        );

        $body = (string)$response->getBody();


        return $this->serializer->deserialize(
            $body,
            BatchInfo::class,
            $job->getContentType() === JobInfo::TYPE_JSON ? 'json' : 'xml'
        );
    }

    /**
     * @param JobInfo $job
     *
     * @return JobInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobStatus(JobInfo $job): JobInfo
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'/'.$job->getId())
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            $job->getContentType() === JobInfo::TYPE_JSON ? 'json' : 'xml'
        );
    }

    /**
     * @param JobInfo $job
     * @param string $batchId
     *
     * @return BatchInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBatchStatus(JobInfo $job, string $batchId): BatchInfo
    {
        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'/'.$job->getId().'/batch/'.$batchId
            )
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            BatchInfo::class,
            $job->getContentType() === JobInfo::TYPE_JSON ? 'json' : 'xml'
        );
    }

    /**
     * @param JobInfo $job
     * @param string $batchId
     *
     * @return array
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBatchResults(JobInfo $job, string $batchId): array
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'/'.$job->getId().'/batch/'.$batchId.'/result')
        );

        $body = (string)$response->getBody();

        if (substr($body, 0, strlen('<result-list')) === '<result-list') {
            $matches = [];
            if (false !== preg_match_all('/<result>(?<id>.*?)<\/result>/', $body, $matches)) {
                return $matches['id'];
            } else {
                return [];
            }
        }

        return $this->serializer->deserialize(
            $body,
            'array',
            $job->getContentType() === JobInfo::TYPE_JSON ? 'json' : 'xml'
        );
    }

    /**
     * @param JobInfo $job
     * @param string $batchId
     * @param string $resultId
     *
     * @return StreamInterface|CsvStream
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResult(JobInfo $job, string $batchId, string $resultId): StreamInterface
    {
        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'/'.$job->getId().'/batch/'.$batchId.'/result/'.$resultId,
                [
                    'save_to' => stream_for(fopen('php://temp', 'w'))
                ]
            )
        );

        if ($job->getContentType() === JobInfo::TYPE_CSV) {
            return new CsvStream($response->getBody());
        }

        return $response->getBody();
    }

    /**
     * @param JobInfo $job
     *
     * @return JobInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function closeJob(JobInfo $job): JobInfo
    {
        $jobInfo = new JobInfo();
        $jobInfo->setState(JobInfo::STATE_CLOSED);

        $response = $this->send(
            new Request(
                "POST",
                self::BASE_PATH.'/'.$job->getId(),
                [],
                $this->serializer->serialize($jobInfo, 'json')
            )
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            'json'
        );
    }
}
