<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 2:03 PM
 */

namespace AE\SalesforceRestSdk\Tests\Composite\Serializer;

use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequest;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Serializer\SObjectHandler;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class SObjectHandlerTest extends TestCase
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
                        $handler->registerSubscribingHandler(new SObjectHandler());
                    }
                )
        ;

        $this->serializer = $builder->build();
    }

    public function testSobjectSerialziationSingle()
    {
        $now                = new \DateTime();
        $sobject            = new SObject();
        $sobject->Type      = "Account";
        $sobject->Name      = 'Test Object';
        $sobject->OwnerId   = 'A10500010129302A10';
        $sobject->CreatedAt = $now;

        $json = $this->serializer->serialize($sobject, 'json');

        $this->assertEquals(
            '{"Type":"Account","Name":"Test Object","OwnerId":"A10500010129302A10","CreatedAt":"'.
            $now->setTimezone(new \DateTimeZone('UTC'))->format(\DATE_ISO8601).'"}',
            $json
        );
    }

    public function testSobjectSerialziationArray()
    {
        $sobject          = new SObject();
        $sobject->Type    = "Account";
        $sobject->Name    = 'Test Object';
        $sobject->OwnerId = 'A10500010129302A10';

        $json = $this->serializer->serialize([$sobject], 'json');

        $this->assertEquals(
            '[{"Type":"Account","Name":"Test Object","OwnerId":"A10500010129302A10"}]',
            $json
        );
    }

    public function testSobjectDeserialize()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        /** @var SObject $sobject */
        $sobject = $this->serializer->deserialize(
            '{"Type":"Account","Name":"Test Object","OwnerId":"A10500010129302A10","CreatedAt":"'.$now->format(
                \DATE_ISO8601
            ).'"}',
            SObject::class,
            'json'
        );

        $this->assertEquals("Account", $sobject->Type);
        $this->assertEquals("Test Object", $sobject->Name);
        $this->assertEquals("A10500010129302A10", $sobject->OwnerId);
        $this->assertInstanceOf(\DateTime::class, $sobject->CreatedAt);
        $this->assertEquals($now->format(\DATE_ISO8601), $sobject->CreatedAt->format(\DATE_ISO8601));
    }

    public function testSobjectDeepSerialize()
    {
        $account       = new SObject();
        $account->Type = "Account";
        $account->Name = "Composite Test Account";

        $contact            = new SObject();
        $contact->Type      = "Contact";
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
            '{"allOrNone":true,"records":[{"Type":"Account","Name":"Composite Test Account"},{"Type":"Contact","FirstName":"Composite","LastName":"Test Contact"}]}',
            $json
        );
    }
}
