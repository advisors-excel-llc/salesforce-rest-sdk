<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 2:34 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection;

use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionRequestInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\CollectionResponse;
use AE\SalesforceRestSdk\Model\Rest\Composite\DeleteSubRequest as BaseSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Rest\Composite\CompositeClient;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class DeleteSubRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\SObject\Collection
 * @property CollectionRequestInterface $body
 */
class DeleteSubRequest extends BaseSubRequest implements CompositeCollectionSubRequestInterface
{
    public function __construct(CollectionRequestInterface $request, $version = "44.0", ?string $referenceId = null)
    {
        parent::__construct($version, $referenceId);

        $this->setBody($request);
    }

    final public function setBody($body): SubRequest
    {
        if (!$body instanceof CollectionRequestInterface) {
            throw new \InvalidArgumentException(
                "The delete request body must be an instance of CollectionRequestInterface"
            );
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

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        $ids = [];

        foreach ($this->body->getRecords() as $record) {
            if (null !== $record->Id) {
                $ids[] = $record->Id;
            }
        }

        if (empty($ids)) {
            throw new \RuntimeException("Cannot make a delete collection request without IDs.");
        }

        $this->url = $this->getBasePath().'?'.http_build_query(
            [
                'allOrNone' => $this->body->isAllOrNone() ? "true" : "false",
                'ids'       => implode(",", $ids),
            ]
        );
    }

    public function getBasePath(): string
    {
        return '/services/data/v'.$this->getVersion().'/composite/sobjects';
    }
}
