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
use AE\SalesforceRestSdk\Rest\AbstractClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\RequestInterface;

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
    public const VERSION = "43.0";

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
        string $concurrencyMode = "Parallel"
    ): JobInfo {
        $jobInfo = new JobInfo();
        $jobInfo->setObject($sObjectType)
                ->setOperation($operation)
                ->setContentType($contentType)
                ->setConcurrencyMode($concurrencyMode)
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
            'json'
        );
    }

    /**
     * @param string $jobId
     *
     * @return JobInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getJobStatus(string $jobId): JobInfo
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'/'.$jobId)
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            'json'
        );
    }

    /**
     * @param string $jobId
     * @param string $batchId
     *
     * @return BatchInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBatchStatus(string $jobId, string $batchId): BatchInfo
    {
        $response = $this->send(
            new Request(
                "GET",
                self::BASE_PATH.'/'.$jobId.'/batch/'.$batchId
            )
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            BatchInfo::class,
            'json'
        );
    }

    /**
     * @param string $jobId
     * @param string $batchId
     *
     * @return array|string[]
     *
     *
     * /**
     *
     * @param string $jobId
     * @param string $batchId
     *
     * @return array
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBatchResults(string $jobId, string $batchId): array
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'/'.$jobId.'/batch/'.$batchId.'/result')
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            'array<string>',
            'json'
        );
    }

    /**
     * @param string $jobId
     * @param string $batchId
     * @param string $resultId
     *
     * @return string
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getResult(string $jobId, string $batchId, string $resultId): string
    {
        $response = $this->send(
            new Request("GET", self::BASE_PATH.'/'.$jobId.'/batch/'.$batchId.'/result/'.$resultId)
        );

        return (string)$response->getBody();
    }

    /**
     * @param string $jobId
     *
     * @return JobInfo
     * @throws SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function closeJob(string $jobId): JobInfo
    {
        $jobInfo = new JobInfo();
        $jobInfo->setState(JobInfo::STATE_CLOSED);

        $response = $this->send(
            new Request("POST", self::BASE_PATH.'/'.$jobId, [], $this->serializer->serialize($jobInfo, 'json'))
        );

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            'json'
        );
    }
}
