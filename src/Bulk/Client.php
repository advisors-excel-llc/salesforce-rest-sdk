<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 5:52 PM
 */

namespace AE\SalesforceRestSdk\Bulk;

use AE\SalesforceRestSdk\AuthProvider\AuthProviderInterface;
use AE\SalesforceRestSdk\Rest\AbstractClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
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
     * @var AuthProviderInterface
     */
    private $authProvider;

    /**
     * Client constructor.
     *
     * @param string $url
     * @param AuthProviderInterface $authProvider
     */
    public function __construct(string $url, AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
        $this->client       = $this->createHttpClient($url);
        $this->serializer   = $this->createSerializer();
    }

    /**
     * @param string $url
     *
     * @return \GuzzleHttp\Client
     */
    protected function createHttpClient(string $url): \GuzzleHttp\Client
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
     */
    protected function authorize(RequestInterface $request): RequestInterface
    {
        $token = explode(' ', $this->authProvider->authorize());

        return $request->withAddedHeader('X-SFDC-Session', array_pop($token));
    }

    /**
     * @param string $sObjectType
     * @param string $contentType
     * @param string $concurrencyMode
     *
     * @return JobInfo
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

        $response = $this->client->post(
            self::BASE_PATH,
            [
                'body' => $this->serializer->serialize($jobInfo, 'json'),
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response, 201);

        $body = (string)$response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            'json'
        );
    }

    /**
     * @param string $jobId
     * @param $batch
     *
     * @return BatchInfo
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

        $response = $this->client->post(
            self::BASE_PATH.'/'.$job->getId().'/batch',
            [
                'headers' => [
                    'Content-Type' => $contentType,
                ],
                'body'    => $batch,
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response, 201);

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
     */
    public function getJobStatus(string $jobId): JobInfo
    {
        $response = $this->client->get(self::BASE_PATH.'/'.$jobId);

        $this->throwErrorIfInvalidResponseCode($response);

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
     */
    public function getBatchStatus(string $jobId, string $batchId): BatchInfo
    {
        $response = $this->client->get(self::BASE_PATH.'/'.$jobId.'/batch/'.$batchId);

        $this->throwErrorIfInvalidResponseCode($response);

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
     */
    public function getBatchResults(string $jobId, string $batchId): array
    {
        $response = $this->client->get(self::BASE_PATH.'/'.$jobId.'/batch/'.$batchId.'/result');

        $this->throwErrorIfInvalidResponseCode($response);

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
     */
    public function getResult(string $jobId, string $batchId, string $resultId): string
    {
        $response = $this->client->get(self::BASE_PATH.'/'.$jobId.'/batch/'.$batchId.'/result/'.$resultId);

        $this->throwErrorIfInvalidResponseCode($response);

        return (string)$response->getBody();
    }

    public function closeJob(string $jobId): JobInfo
    {
        $jobInfo = new JobInfo();
        $jobInfo->setState(JobInfo::STATE_CLOSED);

        $response = $this->client->post(
            self::BASE_PATH.'/'.$jobId,
            [
                'body' => $this->serializer->serialize($jobInfo, 'json')
            ]
        );

        $this->throwErrorIfInvalidResponseCode($response);

        $body = (string) $response->getBody();

        return $this->serializer->deserialize(
            $body,
            JobInfo::class,
            'json'
        );
    }
}
