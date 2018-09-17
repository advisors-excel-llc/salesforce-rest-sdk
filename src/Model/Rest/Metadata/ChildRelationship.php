<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 4:16 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Metadata;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ChildRelationship
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class ChildRelationship
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $cascadeDelete;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $childSObject;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $deprecatedAndHidden;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $field;

    /**
     * @var array|string[]
     * @Serializer\Type("array<string>")
     */
    private $junctionIdListNames = [];

    /**
     * @var array|string{]
     * @Serializer\Type("array<string>")
     */
    private $junctionReferenceTo = [];

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $relationshipName;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $restrictedDelete;

    /**
     * @return bool
     */
    public function isCascadeDelete(): bool
    {
        return $this->cascadeDelete;
    }

    /**
     * @param bool $cascadeDelete
     *
     * @return ChildRelationship
     */
    public function setCascadeDelete(bool $cascadeDelete): ChildRelationship
    {
        $this->cascadeDelete = $cascadeDelete;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getChildSObject(): ?string
    {
        return $this->childSObject;
    }

    /**
     * @param null|string $childSObject
     *
     * @return ChildRelationship
     */
    public function setChildSObject(?string $childSObject): ChildRelationship
    {
        $this->childSObject = $childSObject;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeprecatedAndHidden(): bool
    {
        return $this->deprecatedAndHidden;
    }

    /**
     * @param bool $deprecatedAndHidden
     *
     * @return ChildRelationship
     */
    public function setDeprecatedAndHidden(bool $deprecatedAndHidden): ChildRelationship
    {
        $this->deprecatedAndHidden = $deprecatedAndHidden;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @param null|string $field
     *
     * @return ChildRelationship
     */
    public function setField(?string $field): ChildRelationship
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getJunctionIdListNames()
    {
        return $this->junctionIdListNames;
    }

    /**
     * @param array|string[] $junctionIdListNames
     *
     * @return ChildRelationship
     */
    public function setJunctionIdListNames($junctionIdListNames)
    {
        $this->junctionIdListNames = $junctionIdListNames;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getJunctionReferenceTo()
    {
        return $this->junctionReferenceTo;
    }

    /**
     * @param array|string $junctionReferenceTo
     *
     * @return ChildRelationship
     */
    public function setJunctionReferenceTo($junctionReferenceTo)
    {
        $this->junctionReferenceTo = $junctionReferenceTo;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRelationshipName(): ?string
    {
        return $this->relationshipName;
    }

    /**
     * @param null|string $relationshipName
     *
     * @return ChildRelationship
     */
    public function setRelationshipName(?string $relationshipName): ChildRelationship
    {
        $this->relationshipName = $relationshipName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRestrictedDelete(): bool
    {
        return $this->restrictedDelete;
    }

    /**
     * @param bool $restrictedDelete
     *
     * @return ChildRelationship
     */
    public function setRestrictedDelete(bool $restrictedDelete): ChildRelationship
    {
        $this->restrictedDelete = $restrictedDelete;

        return $this;
    }
}
