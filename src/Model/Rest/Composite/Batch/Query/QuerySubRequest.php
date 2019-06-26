<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 1:38 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Query;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Query\QuerySubRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\QueryResult;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class QuerySubRequest extends GetSubRequest implements QuerySubRequestInterface
{
    /**
     * @var string|QueryResult
     * @Serializer\Exclude()
     */
    protected $query;

    /**
     * QuerySubRequest constructor.
     *
     * @param string|QueryResult $query
     */
    public function __construct($query)
    {
        parent::__construct();
        $this->query = $query;
    }

    final public function setRichInput($body): SubRequest
    {
        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    /**
     * @return QueryResult|string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param QueryResult|string $query
     *
     * @return QuerySubRequest
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    public function getResultClass(): ?string
    {
        return QueryResult::class;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        if ($this->query instanceof QueryResult && null !== $this->query->getNextRecordsUrl()) {
            $this->url = preg_replace('/^\/services\/data\//', '', $this->query->getNextRecordsUrl());
        } elseif (is_string($this->query)) {
            $this->url = 'v'.$this->getVersion().'/query/?'.http_build_query(['q' => $this->query]);
        } else {
            throw new \RuntimeException("INVALID REQUEST: Unable to build the sub request with the given query.");
        }
    }
}
