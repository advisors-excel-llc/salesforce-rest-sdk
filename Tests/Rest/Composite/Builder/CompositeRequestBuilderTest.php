<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 3:16 PM
 */

namespace AE\SalesforceRestSdk\Tests\Rest\Composite\Builder;

use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\CreateSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DeleteSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\UpdateSubRequest;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\Composite\Builder\CompositeRequestBuilder;
use PHPUnit\Framework\TestCase;

class CompositeRequestBuilderTest extends TestCase
{
    public function testBuilder()
    {
        $builder = new CompositeRequestBuilder();

        $builder->createSObject(
            "FirstCreate",
            "Account",
            new SObject([
                "Name" => "Wicked Cool Thang"
            ])
        )
            ->getSObject(
                "GetFirstThang",
                "Account",
                $builder->reference("FirstCreate")->field("Id"),
                [
                    "Id",
                    "Name",
                ]
            )
            ->updateSObject(
                "UpdateFirstThang",
                "Account",
                new SObject([
                    "Id" => $builder->reference("FirstCreate")->field("Id"),
                    "Name" => $builder->reference("GetFirstThang")->field("Name").' 1'
                ])
            )
            ->deleteSObject(
                "DeleteFirstThang",
                "Account",
                $builder->reference("GetFirstThang")->field("Id")
            )
        ;

        $request = $builder->build();

        $subrequests = $request->getCompositeRequest();

        $this->assertEquals(4, count($subrequests));
        /** @var CreateSubRequest $create */
        $create = $subrequests[0];
        $this->assertInstanceOf(CreateSubRequest::class, $create);
        $createSObject = $create->getBody();
        $this->assertInstanceOf(SObject::class, $createSObject);
        $this->assertEquals("Wicked Cool Thang", $createSObject->Name);

        /** @var GetSubRequest $get */
        $get = $subrequests[1];
        $this->assertInstanceOf(GetSubRequest::class, $get);
        $this->assertEquals("Account", $get->getSObjectType());
        $this->assertEquals("@{FirstCreate.Id}", $get->getSObjectId());
        $this->assertArraySubset(["Id", "Name"], $get->getFields());

        /** @var UpdateSubRequest $update */
        $update = $subrequests[2];
        $this->assertInstanceOf(UpdateSubRequest::class, $update);
        $this->assertEquals("@{FirstCreate.Id}", $update->getBody()->Id);
        $this->assertEquals("@{GetFirstThang.Name} 1", $update->getBody()->Name);

        /** @var DeleteSubRequest $delete */
        $delete = $subrequests[3];
        $this->assertInstanceOf(DeleteSubRequest::class, $delete);
        $this->assertEquals("@{GetFirstThang.Id}", $delete->getSObjectId());
    }
}
