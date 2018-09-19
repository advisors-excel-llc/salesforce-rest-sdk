<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/14/18
 * Time: 10:25 AM
 */

namespace AE\SalesforceRestSdk\Tests\Composite;

use AE\SalesforceRestSdk\AuthProvider\LoginProvider;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\Client;
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
            getenv("SF_URL"),
            new LoginProvider(
                getenv("SF_CLIENT_ID"),
                getenv("SF_CLIENT_SECRET"),
                getenv("SF_USER"),
                getenv("SF_PASS"),
                getenv("SF_LOGIN_URL")
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
        $builder = new CompositeRequestBuilder();

        $builder
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
            ->query(
                "QueryForAccount",
                "SELECT Id, Name FROM Account WHERE Name = 'Wicked Cool Thang 1'"
            )
            ->deleteSObject(
                "DeleteFirstThang",
                "Account",
                $builder->reference("QueryForAccount")->field("records[0].Id")
            )
        ;

        $request = $builder->build();

        $response = $this->client->sendCompositeRequest($request);
        $this->assertEquals(5, count($response->getCompositeResponse()));

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

        $query = $response->findResultByReferenceId("QueryForAccount");
        $this->assertNotNull($query);
        $this->assertEquals(200, $query->getHttpStatusCode());
        $this->assertTrue($query->getBody()->isDone());
        $this->assertEquals(1, $query->getBody()->getTotalSize());
        $this->assertEquals(1, count($query->getBody()->getRecords()));

        $delete = $response->findResultByReferenceId("DeleteFirstThang");
        $this->assertNotNull($delete);
        $this->assertEquals(204, $update->getHttpStatusCode());
    }
}
