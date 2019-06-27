<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/21/18
 * Time: 4:02 PM
 */
namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Search;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Query\QuerySubRequest;
use AE\SalesforceRestSdk\Model\Rest\SearchResult;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class SearchSubRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Search
 */
class SearchSubRequest extends QuerySubRequest
{
    public function __construct(string $query)
    {
        parent::__construct($query);
    }

    public function getResultClass(): ?string
    {
        return SearchResult::class;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        $this->url = 'v'.$this->getVersion().'/search?'
            .http_build_query(
                [
                    'q' => $this->query
                ]
            )
        ;
    }
}
