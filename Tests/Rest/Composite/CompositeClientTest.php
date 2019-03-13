<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/14/18
 * Time: 10:25 AM
 */

namespace AE\SalesforceRestSdk\Tests\Composite;

use AE\SalesforceRestSdk\AuthProvider\OAuthProvider;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeCollection;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\Client;
use AE\SalesforceRestSdk\Rest\Composite\Builder\BatchRequestBuilder;
use AE\SalesforceRestSdk\Rest\Composite\Builder\CompositeRequestBuilder;
use AE\SalesforceRestSdk\Rest\Composite\CompositeClient;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequest;
use PHPUnit\Framework\TestCase;

class CompositeClientTest extends TestCase
{
    /**
     * @var CompositeClient
     */
    private $client;

    protected function setUp()
    {
        $client = new Client(
            new OAuthProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_LOGIN_URL"),
                getenv("SF_USER"),
                getenv("SF_PASS")
            )
        );

        $this->client = $client->getCompositeClient();
    }

    public function testCreate()
    {
        $account       = new CompositeSObject('Account');
        $account->Name = "Composite Test Account";

        $contact            = new CompositeSObject('Contact');
        $contact->FirstName = "Composite";
        $contact->LastName  = "Test Contact";

        $request = new CollectionRequest(
            [
                $account,
                $contact,
            ],
            true
        );

        $responses = $this->client->create($request);

        $this->assertEquals(2, count($responses));

        $this->assertTrue($responses[0]->isSuccess());
        $this->assertTrue($responses[1]->isSuccess());
        $this->assertNotNull($responses[0]->getId());
        $this->assertNotNull($responses[1]->getId());

        return [
            'account' => $responses[0]->getId(),
            'contact' => $responses[1]->getId(),
        ];
    }

    /**
     * @depends testCreate
     *
     * @param array $ids
     *
     * @return array
     */
    public function testRead(array $ids)
    {
        $accounts = $this->client->read('Account', [$ids['account']], ['id', 'Name', 'CreatedDate']);

        $this->assertEquals(1, count($accounts));
        $account = $accounts[0];

        $this->assertEquals($ids['account'], $account->Id);
        $this->assertEquals('Composite Test Account', $account->Name);

        $contacts = $this->client->read(
            'Contact',
            [$ids['contact']],
            ['id', 'Name', 'FirstName', 'LastName', 'CreatedDate']
        );

        $this->assertEquals(1, count($contacts));
        $contact = $contacts[0];

        $this->assertEquals($ids['contact'], $contact->Id);
        $this->assertEquals('Composite Test Contact', $contact->Name);

        return $ids;
    }

    /**
     * @depends testRead
     *
     * @param array $ids
     *
     * @return array
     */
    public function testUpdate(array $ids)
    {
        $account = new CompositeSObject('Account', ['id' => $ids['account'], 'Name' => 'Composite Test Update']);
        $contact = new CompositeSObject('Contact', ['id' => $ids['contact'], 'LastName' => 'Test Update']);

        $responses = $this->client->update(
            new CollectionRequest(
                [
                    $account,
                    $contact,
                ]
            )
        );

        $this->assertEquals(2, count($responses));
        $this->assertTrue($responses[0]->isSuccess());
        $this->assertTrue($responses[1]->isSuccess());
        $this->assertEquals($ids['account'], $responses[0]->getId());
        $this->assertEquals($ids['contact'], $responses[1]->getId());

        return $ids;
    }

    /**
     * @depends testUpdate
     *
     * @param array $ids
     */
    public function testDelete(array $ids)
    {
        $responses = $this->client->delete(
            new CollectionRequest(
                [
                    new CompositeSObject('Account', ['id' => $ids['account']]),
                    new CompositeSObject('Contact', ['id' => $ids['contact']]),
                ]
            )
        );

        $this->assertEquals(2, count($responses));
        $this->assertTrue($responses[0]->isSuccess());
        $this->assertTrue($responses[1]->isSuccess());
        $this->assertEquals($ids['account'], $responses[0]->getId());
        $this->assertEquals($ids['contact'], $responses[1]->getId());
    }

    public function testCompositeRequest()
    {
        $now     = new \DateTime();
        $builder = new CompositeRequestBuilder();

        $builder
            ->info("BasicInfo", "Account")
            ->describeGlobal("GlobalDescribe")
            ->describe("DescribeAccount", "Account")
            ->createSObject(
                "FirstCreate",
                "Account",
                new SObject(
                    [
                        "Name" => "Wicked Cool Thang",
                    ]
                )
            )
            ->getSObject(
                "GetFirstThang",
                "Account",
                $builder->reference("FirstCreate")->field("id"),
                [
                    "Id",
                    "Name",
                ]
            )
            ->updateSObject(
                "UpdateFirstThang",
                "Account",
                new SObject(
                    [
                        "Id"   => $builder->reference("FirstCreate")->field("id"),
                        "Name" => $builder->reference("GetFirstThang")->field("Name").' 1',
                    ]
                )
            )
            ->getUpdated(
                "UpdatedAccounts",
                "Account",
                (clone($now))->sub(new \DateInterval("P1D")),
                (clone($now))->add(new \DateInterval('PT1M'))
            )
            ->query(
                "QueryForAccount",
                "SELECT Id, Name FROM Account WHERE Name = 'Wicked Cool Thang 1'"
            )
            ->deleteSObject(
                "DeleteFirstThang",
                "Account",
                $builder->reference("QueryForAccount")->field("records[0].Id")
            )
            ->getDeleted(
                "DeletedAccounts",
                "Account",
                (clone($now))->sub(new \DateInterval("P1D")),
                (clone($now))->add(new \DateInterval('PT1M'))
            )
        ;

        $request = $builder->build();

        $response = $this->client->sendCompositeRequest($request);
        $this->assertCount(10, $response->getCompositeResponse());

        $info = $response->findResultByReferenceId("BasicInfo");
        $this->assertNotNull($info);
        $this->assertEquals(200, $info->getHttpStatusCode());
        $this->assertEquals("Account", $info->getBody()->getObjectDescribe()->getName());

        $describe = $response->findResultByReferenceId("DescribeAccount");
        $this->assertNotNull($describe);
        $this->assertEquals(200, $describe->getHttpStatusCode());
        $this->assertEquals("Account", $describe->getBody()->getName());

        $create = $response->findResultByReferenceId("FirstCreate");
        $this->assertNotNull($create);
        $this->assertEquals(201, $create->getHttpStatusCode());
        $this->assertNotNull($create->getBody());
        $this->assertNotNull($create->getBody()->getId());

        $get = $response->findResultByReferenceId("GetFirstThang");
        $this->assertNotNull($get);
        $this->assertEquals(200, $get->getHttpStatusCode());
        $this->assertNotNull($get->getBody());
        $this->assertNotNull($get->getBody()->Id);
        $this->assertNotNull($get->getBody()->Name);

        $update = $response->findResultByReferenceId("UpdateFirstThang");
        $this->assertNotNull($update);
        $this->assertEquals(204, $update->getHttpStatusCode());

        $updated = $response->findResultByReferenceId("UpdatedAccounts");
        $this->assertNotNull($updated);
        $this->assertEquals(200, $updated->getHttpStatusCode());
        $this->assertNotEmpty($updated->getBody()->getIds());

        $query = $response->findResultByReferenceId("QueryForAccount");
        $this->assertNotNull($query);
        $this->assertEquals(200, $query->getHttpStatusCode());
        $this->assertTrue($query->getBody()->isDone());
        $this->assertEquals(1, $query->getBody()->getTotalSize());
        $this->assertEquals(1, count($query->getBody()->getRecords()));

        $delete = $response->findResultByReferenceId("DeleteFirstThang");
        $this->assertNotNull($delete);
        $this->assertEquals(204, $update->getHttpStatusCode());

        $deleted = $response->findResultByReferenceId("DeletedAccounts");
        $this->assertNotNull($deleted);
        $this->assertEquals(200, $deleted->getHttpStatusCode());
        $this->assertNotEmpty($deleted->getBody()->getDeletedRecords());
    }

    // It's not like this couldn't be rolled into the previous test, but that thing was getting huge
    public function testCompositeCollectionRequest()
    {
        $builder = new CompositeRequestBuilder();

        $builder
            ->createSObjectCollection(
                "create",
                new CollectionRequest(
                    [
                        new CompositeSObject("Account", ["Name" => "Composite Test Account"]),
                        new CompositeSObject(
                            "Contact",
                            [
                                "FirstName" => "Composite",
                                "LastName"  => "Contact",
                            ]
                        ),
                    ]
                )
            )
            // Sadly, you can't use references in getSObjectCollections
            ->getSObject(
                "getAccount",
                "Account",
                $builder->reference("create")->field("id", 0),
                ["Id", "Name"]
            )
            ->getSObject(
                "getContact",
                "Contact",
                $builder->reference("create")->field("id", 1),
                ["Id", "Name", "FirstName", "LastName"]
            )
            ->updateSObjectCollection(
                "update",
                new CollectionRequest(
                    [
                        new CompositeSObject(
                            "Account",
                            [
                                "Id"   => $builder->reference("create")->field("id", 0),
                                "Name" => $builder->reference("getAccount")->field("Name").' 1',
                            ]
                        ),
                        new CompositeSObject(
                            "Contact",
                            [
                                "Id"        => $builder->reference("create")->field("id", 1),
                                "FirstName" => $builder->reference("getContact")->field("LastName"),
                                "LastName"  => $builder->reference("getContact")->field("FirstName"),
                            ]
                        ),
                    ]
                )
            )
            ->deleteSObject("deleteAccount", "Account", $builder->reference("create")->field("id", 0))
            ->deleteSObject("deleteContact", "Contact", $builder->reference("create")->field("id", 1))
        ;

        $response = $this->client->sendCompositeRequest($builder->build());

        $this->assertCount(7, $response->getCompositeResponse());

        $create = $response->findResultByReferenceId("create");
        $this->assertNotNull($create);
        $this->assertEquals(200, $create->getHttpStatusCode());
        $this->assertCount(2, $create->getBody());
        /** @var CollectionResponse[] $records */
        $records = $create->getBody();
        $this->assertTrue($records[0]->isSuccess());
        $this->assertTrue($records[1]->isSuccess());

        $getAccounts = $response->findResultByReferenceId("getAccount");
        $this->assertNotNull($getAccounts);
        $this->assertEquals(200, $getAccounts->getHttpStatusCode());
        /** @var SObject $account */
        $account = $getAccounts->getBody();
        $this->assertEquals("Composite Test Account", $account->Name);

        $getContacts = $response->findResultByReferenceId("getContact");
        $this->assertNotNull($getContacts);
        $this->assertEquals(200, $getContacts->getHttpStatusCode());
        /** @var SObject $contact */
        $contact = $getContacts->getBody();
        $this->assertEquals("Composite", $contact->FirstName);
        $this->assertEquals("Contact", $contact->LastName);

        $update = $response->findResultByReferenceId("update");
        $this->assertNotNull($update);
        $this->assertEquals(200, $update->getHttpStatusCode());
        /** @var CollectionResponse[] $updated */
        $updated = $update->getBody();
        $this->assertTrue($updated[0]->isSuccess());
        $this->assertTrue($updated[1]->isSuccess());

        $deleteAccount = $response->findResultByReferenceId("deleteAccount");
        $this->assertNotNull($deleteAccount);
        $this->assertEquals(204, $deleteAccount->getHttpStatusCode());

        $deleteContact = $response->findResultByReferenceId("deleteContact");
        $this->assertNotNull($deleteContact);
        $this->assertEquals(204, $deleteContact->getHttpStatusCode());
    }

    public function testTree()
    {
        $tree = new CompositeCollection(
            [
                new CompositeSObject(
                    "Account",
                    [
                        "referenceId" => "account1",
                        "Name"        => "Composite Tree Account",
                        "Contacts"    => new CompositeCollection(
                            [
                                new CompositeSObject(
                                    "Contact",
                                    [
                                        "referenceId" => "contact1",
                                        "FirstName"   => "Composite",
                                        "LastName"    => "Tree Contact",
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $response = $this->client->tree("Account", $tree);

        $this->assertFalse($response->isHasErrors());
        $results = $response->getResults();
        $this->assertCount(2, $results);
        $this->assertEquals("account1", $results[0]->getReferenceId());
        $this->assertNotNull($results[0]->getId());
        $this->assertEquals("contact1", $results[1]->getReferenceId());
        $this->assertNotNull($results[1]->getId());

        // A Good Programmer always cleans up after themselves
        $this->client->delete(
            new CollectionRequest(
                [
                    new CompositeSObject("Account", ["Id" => $results[0]->getId()]),
                    new CompositeSObject("Contact", ["Id" => $results[1]->getId()]),
                ]
            )
        );
    }

    public function testBatchSObject()
    {
        $builder = new BatchRequestBuilder();

        $builder
            ->limits()
            ->describe("Account")
            ->query("SELECT Id, Name FROM Account")
            ->createSObject(
                "Account",
                new SObject(
                    [
                        "Name" => "Batch Test Account",
                    ]
                )
            )
            ->search("FIND {Batch Test Account}")
        ;

        $firstResponse = $this->client->batch($builder->build());

        $results = $firstResponse->getResults();
        $this->assertCount(5, $results);

        $limits = $results[0];
        $this->assertEquals(200, $limits->getStatusCode());
        $this->assertGreaterThan(0, $limits->getResult()->getDailyApiRequests()->getRemaining());

        $describe = $results[1];
        $this->assertEquals(200, $describe->getStatusCode());
        $this->assertEquals("Account", $describe->getResult()->getName());

        $query = $results[2];
        $this->assertEquals(200, $query->getStatusCode());
        $this->assertGreaterThan(0, $query->getResult()->getTotalSize());

        $create = $results[3];
        $this->assertEquals(201, $create->getStatusCode());
        $this->assertTrue($create->getResult()->isSuccess());
        $id = $create->getResult()->getId();
        $this->assertNotNull($id);

        $search = $results[4];
        $this->assertEquals(200, $search->getStatusCode());
        $this->assertNotEmpty($search->getResult()->getSearchRecords());

        return $id;
    }

    /**
     * @param string $id
     *
     * @depends testBatchSObject
     */
    public function testBatchSObject2(string $id)
    {
        $builder = new BatchRequestBuilder();
        $now     = new \DateTime();

        $builder
            ->getSObject("Account", $id, ["Id", "Name"])
            ->updateSObject(
                "Account",
                new SObject(
                    [
                        "Id"   => $id,
                        "Name" => "Batch Test Update",
                    ]
                )
            )
            ->getUpdated("Account", $now, (clone($now))->add(new \DateInterval('PT2M')))
            ->deleteSObject("Account", $id)
            ->getDeleted(
                "Account",
                (clone($now))->sub(new \DateInterval('PT10M')),
                (clone($now))->add(new \DateInterval('PT2M'))
            )
        ;

        $response = $this->client->batch($builder->build());
        $results  = $response->getResults();

        $this->assertCount(5, $results);

        $get = $results[0];
        $this->assertEquals(200, $get->getStatusCode());
        $this->assertEquals($id, $get->getResult()->Id);
        $this->assertEquals("Batch Test Account", $get->getResult()->Name);

        $update = $results[1];
        $this->assertEquals(204, $update->getStatusCode());
        $this->assertNull($update->getResult());

        $updated = $results[2];
        $this->assertEquals(200, $updated->getStatusCode());
        $this->assertNotEmpty($updated->getResult());

        $delete = $results[3];
        $this->assertEquals(204, $delete->getStatusCode());
        $this->assertNull($delete->getResult());

        $deleted = $results[4];
        $this->assertEquals(200, $deleted->getStatusCode());
        $this->assertNotEmpty($deleted->getResult()->getDeletedRecords());
    }
}
