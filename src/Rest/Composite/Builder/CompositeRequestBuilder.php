<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 10:52 AM
 */

namespace AE\SalesforceRestSdk\Rest\Composite\Builder;

use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Limit\LimitSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Query\QueryAllSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Query\QuerySubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\BasicInfoSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection\CompositeCollectionSubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection\ReadSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\CreateSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DeleteSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DescribeGlobalSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\DescribeSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetDeletedSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Query\QuerySubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\ReferenceableInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\GetUpdatedSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\UpdateSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\QueryResult;
use AE\SalesforceRestSdk\Model\SObject;
use Doctrine\Common\Collections\ArrayCollection;

class CompositeRequestBuilder implements RequestBuilderInterface, ReferenceableInterface
{
    /**
     * @var ArrayCollection|SubRequest
     */
    private $requests;

    /**
     * @var ArrayCollection|Reference[]
     */
    private $references;

    public function __construct()
    {
        $this->requests   = new ArrayCollection();
        $this->references = new ArrayCollection();
    }

    // QUERY

    /**
     * @param string $referenceId
     * @param string|QueryResult $query
     *
     * @return CompositeRequestBuilder
     */
    public function query(string $referenceId, $query): CompositeRequestBuilder
    {
        return $this->addSubRequest(new QuerySubRequest($query, $referenceId));
    }

    /**
     * @param string $referenceId
     * @param string|QueryResult $query
     *
     * @return CompositeRequestBuilder
     */
    public function queryAll(string $referenceId, $query): CompositeRequestBuilder
    {
        return $this->addSubRequest(new QueryAllSubRequest($query, $referenceId));
    }

    // SOBJECT

    public function info(string $referenceId, string $sObjectType): CompositeRequestBuilder
    {
        return $this->addSubRequest(new BasicInfoSubRequest($sObjectType, $referenceId));
    }

    public function describe(string $referenceId, string $sObjectType): CompositeRequestBuilder
    {
        return $this->addSubRequest(new DescribeSubRequest($sObjectType, $referenceId));
    }

    public function describeGlobal(string $referenceId): CompositeRequestBuilder
    {
        return $this->addSubRequest(new DescribeGlobalSubRequest($referenceId));
    }

    public function getSObject(
        string $referenceId,
        string $sObjectType,
        string $sObjectId,
        array $fields = ["Id"]
    ): CompositeRequestBuilder {
        $request = new GetSubRequest($sObjectType, $sObjectId, $fields, $referenceId);

        $this->addSubRequest($request);

        return $this;
    }

    public function getUpdated(
        string $referenceId,
        string $sObjectType,
        \DateTime $start,
        ?\DateTime $end = null
    ): CompositeRequestBuilder {
        return $this->addSubRequest(
            new GetUpdatedSubRequest($sObjectType, $start, $end, $referenceId)
        );
    }

    public function getDeleted(
        string $referenceId,
        string $sObjectType,
        \DateTime $start,
        ?\DateTime $end
    ): CompositeRequestBuilder {
        return $this->addSubRequest(
            new GetDeletedSubRequest(
                $sObjectType,
                $start,
                $end,
                $referenceId
            )
        );
    }

    public function createSObject(string $referenceId, string $sObjectType, SObject $sObject): CompositeRequestBuilder
    {
        $request = new CreateSubRequest($sObjectType, $referenceId);
        $request->setBody($sObject);

        return $this->addSubRequest($request);
    }

    public function updateSObject(string $referenceId, string $sObjectType, SObject $sObject): CompositeRequestBuilder
    {
        if (null === $sObject->Id) {
            throw new \RuntimeException(
                "The Id field of the SObject must be set. The value can be an Id or a reference to an Id."
            );
        }

        return $this->addSubRequest(new UpdateSubRequest($sObjectType, $sObject, $referenceId));
    }

    public function deleteSObject(string $referenceId, string $sObjectType, string $sObjectId): CompositeRequestBuilder
    {
        return $this->addSubRequest(new DeleteSubRequest($sObjectType, $sObjectId, $referenceId));
    }

    // SOBJECT COLLECTIONS

    public function getSObjectCollection(
        string $referenceId,
        string $sObjectType,
        array $ids,
        array $fields = ["Id"]
    ): CompositeRequestBuilder {
        return $this->addSubRequest(
            new ReadSubRequest($sObjectType, $ids, $fields, $referenceId)
        );
    }

    public function createSObjectCollection(
        string $referenceId,
        CollectionRequestInterface $request
    ): CompositeRequestBuilder {
        return $this->addSubRequest(
            new \AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection\CreateSubRequest(
                $request,
                $referenceId
            )
        );
    }

    public function updateSObjectCollection(
        string $referenceId,
        CollectionRequestInterface $request
    ): CompositeRequestBuilder {
        return $this->addSubRequest(
            new \AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection\UpdateSubRequest(
                $request,
                $referenceId
            )
        );
    }

    public function deleteSObjectCollection(
        string $referenceId,
        CollectionRequestInterface $request
    ): CompositeRequestBuilder {
        return $this->addSubRequest(
            new \AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection\DeleteSubRequest(
                $request,
                $referenceId
            )
        );
    }

    // REGULAR STUFF

    public function addSubRequest(SubRequest $subRequest): CompositeRequestBuilder
    {
        $this->requests->set($subRequest->getReferenceId(), $subRequest);

        return $this;
    }

    /**
     * @param string $referenceId
     *
     * @return SubRequest|null
     */
    public function getSubRequest(string $referenceId): ?SubRequest
    {
        if ($this->requests->containsKey($referenceId)) {
            return $this->requests->get($referenceId);
        }

        return null;
    }

    /**
     * @param string $referenceId
     *
     * @return Reference|ArrayReference
     */
    public function reference(string $referenceId): Reference
    {
        if ($this->references->containsKey($referenceId)) {
            return $this->references->get($referenceId);
        }

        if (!$this->requests->containsKey($referenceId)) {
            throw new \RuntimeException("No SubRequest with reference ID $referenceId is found.");
        }

        $request = $this->requests->get($referenceId);

        $reference = $request instanceof CompositeCollectionSubRequestInterface
            ? new ArrayReference($referenceId)
            : new Reference($referenceId);

        $this->references->set($referenceId, $reference);

        return $reference;
    }

    public function validate()
    {
        if (count($this->requests) > 25) {
            throw new \RuntimeException("A CompositeRequest cannot handle more than 25 subrequests.");
        }

        $queries = $this->requests->filter(
            function ($request) {
                return $request instanceof QuerySubRequestInterface;
            }
        );

        if (count($queries) > 5) {
            throw new \RuntimeException("A CompositeRequest cannot have more than 5 Query subrequests.");
        }
    }

    /**
     * @param bool $allOrNone
     *
     * @return CompositeRequest
     */
    public function build(bool $allOrNone = false): CompositeRequest
    {
        $this->validate();

        $request = new CompositeRequest();
        $request->setAllOrNone($allOrNone);
        $request->setCompositeRequest($this->requests->getValues());

        return $request;
    }
}
