<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 2:12 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection;

use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\PostSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Rest\Composite\CompositeClient;

class CreateSubRequest extends PostSubRequest implements CompositeCollectionSubRequestInterface
{
    public function __construct(CollectionRequestInterface $request, ?string $referenceId = null)
    {
        parent::__construct($referenceId);

        $this->setBody($request);
        $this->url = '/'.CompositeClient::BASE_PATH.'/sobjects';
    }

    final public function setBody($body): SubRequest
    {
        if (!($body instanceof CollectionRequestInterface)) {
            throw new \InvalidArgumentException("Body is expected to be a CollectionRequestInterface.");
        }

        return parent::setBody($body);
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }
}
