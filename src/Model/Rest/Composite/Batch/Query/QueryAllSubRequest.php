<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 1:38 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\Query;

use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class QueryAllSubRequest extends QuerySubRequest
{
    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        if (is_string($this->query)) {
            $this->url = 'v'.Client::VERSION.'queryAll/?'.http_build_query(['q' => $this->query]);
        } else {
            parent::preSerialize();
        }
    }
}
