<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 6:44 PM
 */

namespace AE\SalesforceRestSdk\Tests\Bulk;

use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use AE\SalesforceRestSdk\Bulk\BatchInfo;
use AE\SalesforceRestSdk\Bulk\Client;
use AE\SalesforceRestSdk\Bulk\JobInfo;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Psr7\CsvStream;
use AE\SalesforceRestSdk\Serializer\CompositeSObjectHandler;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class BulkClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     */
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->client = new Client(
            new OAuthProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_LOGIN_URL"),
                getenv("SF_USER"),
                getenv("SF_PASS")
            ),
            "45.0"
        );

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

        $this->serializer = $builder->build();
    }

    public function testBulk()
    {
        $job = $this->client->createJob("Account", JobInfo::QUERY, JobInfo::TYPE_JSON);

        $this->assertNotNull($job->getId());
        $this->assertEquals(JobInfo::STATE_OPEN, $job->getState());

        $batch = $this->client->addBatch($job, "SELECT Id, Name From Account");

        $this->assertNotNull($batch->getId());

        do {
            $batchStatus = $this->client->getBatchStatus($job, $batch->getId());
            sleep(10);
        } while (BatchInfo::STATE_COMPLETED !== $batchStatus->getState());

        $batchResults = $this->client->getBatchResults($job, $batch->getId());
        $this->assertCount(1, $batchResults);
        $resultId = $batchResults[0];

        $result = $this->client->getResult($job, $batch->getId(), $resultId);

        $this->assertNotNull($result);

        $objects = $this->serializer->deserialize(
            (string)$result,
            'array<'.CompositeSObject::class.'>',
            'json'
        );

        $this->assertNotEmpty($objects);
        $this->assertNotNull($objects[0]->Id);
        $this->assertNotNull($objects[0]->Name);

        $closeJob = $this->client->closeJob($job);
        $this->assertEquals($job->getId(), $closeJob->getId());
        $this->assertEquals(JobInfo::STATE_CLOSED, $closeJob->getState());
    }

    public function testCSV()
    {
        $job = $this->client->createJob("Account", JobInfo::QUERY, JobInfo::TYPE_CSV);

        $this->assertNotNull($job->getId());
        $this->assertEquals(JobInfo::STATE_OPEN, $job->getState());

        $batch = $this->client->addBatch($job, "SELECT Id, Name From Account");

        $this->assertNotNull($batch->getId());

        do {
            $batchStatus = $this->client->getBatchStatus($job, $batch->getId());
            sleep(10);
        } while (BatchInfo::STATE_COMPLETED !== $batchStatus->getState());

        $batchResults = $this->client->getBatchResults($job, $batch->getId());
        $this->assertCount(1, $batchResults);
        $resultId = $batchResults[0];

        /** @var CsvStream $result */
        $result = $this->client->getResult($job, $batch->getId(), $resultId);

        $this->assertNotNull($result);

        $closeJob = $this->client->closeJob($job);
        $this->assertEquals($job->getId(), $closeJob->getId());
        $this->assertEquals(JobInfo::STATE_CLOSED, $closeJob->getState());

        $object = $result->getContents(true)->current();

        $this->assertNotEmpty($object);
        $this->assertNotNull($object["Id"]);
        $this->assertNotNull($object["Name"]);
    }
}
