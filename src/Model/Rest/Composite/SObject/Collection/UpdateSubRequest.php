<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 2:23 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection;

use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\PatchSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Rest\Composite\CompositeClient;

class UpdateSubRequest extends PatchSubRequest implements CompositeCollectionSubRequestInterface
{
    public function __construct(
        CollectionRequestInterface $request,
        string $version = "44.0",
        ?string $referenceId = null
    ) {
        parent::__construct($version, $referenceId);

        $this->setBody($request);
        $this->url = $this->getBasePath();
    }

    final public function setBody($body): SubRequest
    {
        if (!($body instanceof CollectionRequestInterface)) {
            throw new \InvalidArgumentException("Body is expected to be a CollectionRequestInterface");
        }

        return parent::setBody($body);
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    public function getResultClass(): ?string
    {
        return 'array<'.CollectionResponse::class.'>';
    }

    public function getBasePath(): string
    {
        return '/services/data/v'.$this->getVersion().'/composite/sobjects';
    }
}
