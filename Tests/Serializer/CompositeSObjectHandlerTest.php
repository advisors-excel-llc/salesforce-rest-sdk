<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 2:03 PM
 */

namespace AE\SalesforceRestSdk\Tests\Serializer;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequest;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Serializer\CompositeSObjectHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class CompositeSObjectHandlerTest extends TestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        $builder = SerializerBuilder::create();
        $builder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $builder->addDefaultHandlers()
                ->addDefaultListeners()
                ->addDefaultDeserializationVisitors()
                ->addDefaultSerializationVisitors()
                ->configureHandlers(
                    function (HandlerRegistry $handler) {
                        $handler->registerSubscribingHandler(new CompositeSObjectHandler());
                    }
                )
        ;

        $this->serializer = $builder->build();
    }

    public function testSobjectSerialziationSingle()
    {
        $sobject = new CompositeSObject("Account");

        $sobject->Name    = 'Test Object';
        $sobject->OwnerId = 'A10500010129302A10';

        $json = $this->serializer->serialize($sobject, 'json');

        $this->assertEquals(
            '{"attributes":{"type":"Account"},"Name":"Test Object","OwnerId":"A10500010129302A10"}',
            $json
        );
    }

    public function testSobjectSerialziationArray()
    {
        $sobject = new CompositeSObject("Account");
        $now = new \DateTime();

        $sobject->Name    = 'Test Object';
        $sobject->OwnerId = 'A10500010129302A10';
        $sobject->CreatedAt = $now;

        $json = $this->serializer->serialize([$sobject], 'json');

        $this->assertEquals(
            '[{"attributes":{"type":"Account"},"Name":"Test Object","OwnerId":"A10500010129302A10","CreatedAt":"'
            .$now->setTimezone(new \DateTimeZone('UTC'))->format(\DATE_ISO8601)
            .'"}]',
            $json
        );
    }

    public function testSobjectDeserialize()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        /** @var SObject $sobject */
        $sobject = $this->serializer->deserialize(
            '{"attributes":{"type":"Account","url":"/test/url"},"Name":"Test Object","OwnerId":"A10500010129302A10","CreatedAt":"'.$now->format(
                \DATE_ISO8601
            ).'"}',
            CompositeSObject::class,
            'json'
        );

        $this->assertEquals("Account", $sobject->getType());
        $this->assertEquals("/test/url", $sobject->getUrl());
        $this->assertEquals("Test Object", $sobject->Name);
        $this->assertEquals("A10500010129302A10", $sobject->OwnerId);
        $this->assertInstanceOf(\DateTime::class, $sobject->CreatedAt);
        $this->assertEquals($now->format(\DATE_ISO8601), $sobject->CreatedAt->format(\DATE_ISO8601));
    }

    public function testSobjectDeepSerialize()
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

        $json = $this->serializer->serialize($request, 'json');

        $this->assertEquals(
            '{"allOrNone":true,"records":[{"attributes":{"type":"Account"},"Name":"Composite Test Account"},{"attributes":{"type":"Contact"},"FirstName":"Composite","LastName":"Test Contact"}]}',
            $json
        );
    }

    public function testDeepObject()
    {
        $do = new DeepObject();
        $do->setName('Nemo');
        $do->setDescription('A Fish');
        $account = new CompositeSObject("Account", [
            'Name' => 'Test Object',
            'deepObject' => $do
        ]);

        $data = $this->serializer->serialize($account, 'json');
        $this->assertEquals(
            '{"attributes":{"type":"Account"},"Name":"Test Object","DeepObject":{"name":"Nemo","description":"A Fish"}}',
            $data
        );

        $des = $this->serializer->deserialize($data, CompositeSObject::class, 'json');

        // In order to denormalize class objects, you'll have to handle that manually
        $this->assertEquals('Nemo', $des->DeepObject['name']);
        $this->assertEquals('A Fish', $des->DeepObject['description']);
    }
}
