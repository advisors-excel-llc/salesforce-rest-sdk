<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 11:49 AM
 */

namespace AE\SalesforceRestSdk\Tests\Rest\SObject;

use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SObjectClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $client = new \AE\SalesforceRestSdk\Rest\Client(
            new OAuthProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_LOGIN_URL"),
                getenv("SF_USER"),
                getenv("SF_PASS")
            )
        );

        $this->client = $client->getSObjectClient();
    }

    public function testInfo()
    {
        $info = $this->client->info("Account");

        $this->assertNotNull($info);

        $describe = $info->getObjectDescribe();

        $this->assertEquals("Account", $describe->getName());
    }

    public function testDescribe()
    {
        $describe = $this->client->describe("Account");

        $this->assertNotNull($describe);
        $this->assertEquals("Account", $describe->getName());
        $this->assertNotEmpty($describe->getFields());
    }

    public function testDescribeGlobal()
    {
        $describe = $this->client->describeGlobal();

        $this->assertNotNull($describe);
        $this->assertNotNull($describe->getEncoding());
        $this->assertGreaterThan(0, $describe->getMaxBatchSize());
        $objects = $describe->getSobjects();
        $this->assertNotEmpty($objects);
        $this->assertNotNull($objects[0]->getName());
    }

    public function testCreate()
    {
        $account       = new SObject();
        $account->Name = "Test Client ".Uuid::uuid4()->toString();

        $saved = $this->client->persist("Account", $account);

        $this->assertTrue($saved);
        $this->assertNotNull($account->Id);
        $this->assertEquals("Account", $account->Type);

        return $account;
    }

    /**
     * @param SObject $SObject
     * @depends testCreate
     * @return SObject
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGet(SObject $SObject): SObject
    {
        $account = $this->client->get("Account", $SObject->Id, ['Id', 'Name']);

        $this->assertNotNull($account);
        $this->assertEquals($SObject->Id, $account->Id);
        $this->assertEquals($SObject->Name, $account->Name);

        return $SObject;
    }

    /**
     * @param SObject $SObject
     * @depends testGet
     * @return SObject
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testQuery(SObject $SObject): SObject
    {
        $query  = "SELECT Id, Name FROM Account WHERE Id = '{$SObject->Id}'";
        $result = $this->client->query($query);

        $this->assertNotNull($result);
        $this->assertTrue($result->isDone());
        $this->assertEquals(1, $result->getTotalSize());
        $records = $result->getRecords();
        $this->assertNotEmpty($records);

        $account = $records[0];

        $this->assertEquals($SObject->Id, $account->Id);
        $this->assertEquals($SObject->Name, $account->Name);
        $this->assertEquals("Account", $account->getType());

        return $SObject;
    }

    /**
     * @param SObject $SObject
     * @depends testQuery
     * @return SObject
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSearch(SObject $SObject): SObject
    {
        $name = preg_quote($SObject->Name);
        $query  = "FIND {{$name}} RETURNING Account(Id,Name)";

        try {
            $result = $this->client->search($query);
        } catch (\RuntimeException $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        $this->assertNotNull($result);
        $records = $result->getSearchRecords();

        $this->assertNotEmpty($records);
        $account = $records[0];

        $this->assertEquals($SObject->Id, $account->Id);
        $this->assertEquals($SObject->Name, $account->Name);
        $this->assertEquals("Account", $account->getType());

        return $SObject;
    }

    /**
     * @param SObject $SObject
     * @depends testSearch
     * @return SObject
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUpdate(SObject $SObject): SObject
    {
        $SObject->Name = $SObject->Name.' 1';

        try {
            $this->client->persist("Account", $SObject);
            $this->assertNotNull($SObject->Id);
            $this->assertEquals("Account", $SObject->Type);
        } catch (\RuntimeException $e) {
            $this->assertTrue(false, $e->getMessage());
        }

        return $SObject;
    }

    /**
     * @param SObject $SObject
     * @depends testUpdate
     * @throws \AE\SalesforceRestSdk\AuthProvider\SessionExpiredOrInvalidException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDelete(SObject $SObject)
    {
        try {
            $this->client->remove("Account", $SObject);
            $this->assertTrue(true);
        } catch (\RuntimeException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
