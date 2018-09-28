<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 2:12 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite\Builder;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\BatchRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Limit\LimitsSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Query\QueryAllSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Query\QuerySubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Search\SearchSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\CreateSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\DeleteSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\DescribeSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\GetDeletedSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\GetUpdatedSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject\UpdateSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\SObject;
use Doctrine\Common\Collections\ArrayCollection;

class BatchRequestBuilder implements RequestBuilderInterface
{
    private $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
    }

    public function limits(): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new LimitsSubRequest()
        );
    }

    public function query(string $query): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new QuerySubRequest($query)
        );
    }

    public function queryAll(string $query): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new QueryAllSubRequest($query)
        );
    }

    public function search(string $query): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new SearchSubRequest($query)
        );
    }

    public function describe(string $sObjectType): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new DescribeSubRequest($sObjectType)
        );
    }

    public function getSObject(string $sObjectType, string $sObjectId, array $fields): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new GetSubRequest($sObjectType, $sObjectId, $fields)
        );
    }

    public function getUpdated(string $sObjectType, \DateTime $start, ?\DateTime $end = null): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new GetUpdatedSubRequest(
                $sObjectType,
                $start,
                $end
            )
        );
    }

    public function getDeleted(string $sObjectType, \DateTime $start, ?\DateTime $end = null): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new GetDeletedSubRequest(
                $sObjectType,
                $start,
                $end
            )
        );
    }

    public function createSObject(string $sObjectType, SObject $sObject): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new CreateSubRequest($sObjectType, $sObject)
        );
    }

    public function updateSObject(string $sObjectType, SObject $sObject): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new UpdateSubRequest(
                $sObjectType,
                $sObject
            )
        );
    }

    public function deleteSObject(string $sObjectType, string $sObjectId): BatchRequestBuilder
    {
        return $this->addSubRequest(
            new DeleteSubRequest($sObjectType, $sObjectId)
        );
    }

    public function addSubRequest(SubRequest $request): BatchRequestBuilder
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
        }

        return $this;
    }

    public function build(): BatchRequest
    {
        if (count($this->requests) > 25) {
            throw new \RuntimeException("A batch request cannot have more than 25 subrequests.");
        }

        return new BatchRequest($this->requests->getValues());
    }

    public function getRequests(): ArrayCollection
    {
        return $this->requests;
    }
}
