<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/18/18
 * Time: 4:20 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\GetSubRequest as BaseSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use AE\SalesforceRestSdk\Model\Rest\Composite\SObject\CompositeSObjectSubRequestInterface;
use AE\SalesforceRestSdk\Model\SObject;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class GetSubRequest
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite\Batch\SObject
 */
class GetSubRequest extends BaseSubRequest implements CompositeSObjectSubRequestInterface
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

    public function __construct(string $sObjectType, string $sObjectId, array $fields = ["Id"])
    {
        parent::__construct();

        $this->sObjectType = $sObjectType;
        $this->sObjectId   = $sObjectId;
        $this->fields      = $fields;
    }

    final public function setRichInput($richInput): SubRequest
    {
        if ($richInput instanceof SObject) {
            $this->fields = array_keys($richInput->getFields());

            if (null !== $richInput->Id) {
                $this->sObjectId = $richInput->Id;
            }

            if (null !== $richInput->Type) {
                $this->sObjectType = $richInput->Type;
            }
        }

        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        if (null === $this->sObjectType || null === $this->sObjectId) {
            throw new \RuntimeException("The GetSubRequest is incomplete.");
        }

        $this->url = 'v'.Client::VERSION.'/sobjects/'.$this->sObjectType.'/'.$this->sObjectId.'?'
            .http_build_query([
                "fields" => implode(",", $this->fields)
            ])
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

    public function getResultClass(): ?string
    {
        return CompositeSObject::class;
    }
}
