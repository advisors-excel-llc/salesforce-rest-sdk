<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 2:03 PM
 */

namespace AE\SalesforceRestSdk\Tests\Composite\Serializer;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Rest\Composite\CompositeRequest;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Serializer\CompositeSObjectHandler;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
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

        $sobject->Name    = 'Test Object';
        $sobject->OwnerId = 'A10500010129302A10';

        $json = $this->serializer->serialize([$sobject], 'json');

        $this->assertEquals(
            '[{"attributes":{"type":"Account"},"Name":"Test Object","OwnerId":"A10500010129302A10"}]',
            $json
        );
    }

    public function testSobjectDeserialize()
    {
        /** @var SObject $sobject */
        $sobject = $this->serializer->deserialize(
            '{"attributes":{"type":"Account","url":"/test/url"},"Name":"Test Object","OwnerId":"A10500010129302A10"}',
            CompositeSObject::class,
            'json'
        );

        $this->assertEquals("Account", $sobject->getType());
        $this->assertEquals("/test/url", $sobject->getUrl());
        $this->assertEquals("Test Object", $sobject->Name);
        $this->assertEquals("A10500010129302A10", $sobject->OwnerId);
    }

    public function testSobjectDeepSerialize()
    {
        $account       = new CompositeSObject('Account');
        $account->Name = "Composite Test Account";

        $contact            = new CompositeSObject('Contact');
        $contact->FirstName = "Composite";
        $contact->LastName  = "Test Contact";

        $request = new CompositeRequest(
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
}
