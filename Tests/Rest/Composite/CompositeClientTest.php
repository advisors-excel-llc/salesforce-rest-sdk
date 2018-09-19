<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/14/18
 * Time: 10:25 AM
 */

namespace AE\SalesforceRestSdk\Tests\Composite\Client;

use AE\SalesforceRestSdk\AuthProvider\LoginProvider;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Rest\Client;
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
}
