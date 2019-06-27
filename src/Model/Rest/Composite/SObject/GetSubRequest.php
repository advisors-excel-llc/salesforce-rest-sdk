<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:20 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\GetSubRequest as BaseSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\ReferenceableInterface;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetSubRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject
 */
class GetSubRequest extends BaseSubRequest implements ReferenceableInterface, SObjectSubRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectId;

    /**
     * @var array
     * @Serializer\Exclude()
     */
    private $fields = ["Id"];

    public function __construct(
        string $sObjectType,
        string $sObjectId,
        array $fields = ["Id"],
        string $version = "44.0",
        ?string $referenceId = null
    ) {
        $this->sObjectType = $sObjectType;
        $this->sObjectId   = $sObjectId;
        $this->fields      = $fields;

        parent::__construct($version, $referenceId);
    }

    final public function setRichInput($richInput): SubRequest
    {
        if ($richInput instanceof SObject) {
            $this->fields = array_keys($richInput->getFields());

            if (null !== $richInput->Id) {
                $this->sObjectId = $richInput->Id;
            }
        }

        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    public function getResultClass(): ?string
    {
        return SObject::class;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        if (null === $this->sObjectType || null === $this->sObjectId) {
            throw new \RuntimeException("The GetSubRequest is incomplete.");
        }

        $this->url = $this->getBasePath().$this->sObjectType.'/'.$this->sObjectId.'?'
            .http_build_query(
                [
                    'fields' => implode(",", $this->fields)
                ]
            )
        ;
    }

    public function reference(string $fieldName): ?string
    {
        $name = ucwords($fieldName);
        if (array_key_exists($name, $this->fields)) {
            return "@{{$this->referenceId}.{$name}}";
        }

        return null;
    }

    /**
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }

    /**
     * @return string
     */
    public function getSObjectId(): string
    {
        return $this->sObjectId;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getBasePath(): string
    {
        return "/services/data/v".$this->getVersion()."/sobjects/";
    }
}
