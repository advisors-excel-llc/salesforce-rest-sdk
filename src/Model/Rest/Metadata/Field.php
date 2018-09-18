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
 * Class Field
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class Field
{
    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $aggregatable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $aiPredictionField;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $autoNumber;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $byteLength = 0;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $calculated;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $calculatedFormula;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $cascadeDelete;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $caseSensitive;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $compoundFieldName;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $controllerName;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $createable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $custom;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $defaultValue;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $defaultValueFormula;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $defaultedOnCreate;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $dependentPicklist;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $deprecatedAndHidden;

    /**
     * @var int
     * @Serializer\Type("int")
     */
    private $digits = 0;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $displayLocationInDecimal;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $encrypted;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $externalId;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $extraTypeInfo;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $filterable;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $filterableLookupInfo;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $forumlaTreatNullNumberAsZero;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $groupable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $highScaleNumber;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $htmlFormatted;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $idLookup;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $inlineHelpText;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $label;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $length;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $mask;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $maskType;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $nameField;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $namePointing;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $nillable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $permissionable;

    /**
     * @var array|PickListValue[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Metadata\PickListValue>")
     */
    private $picklistValues = [];

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $polymorphicForeignKey;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $precision;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $queryByDistance;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $referenceTargetField;

    /**
     * @var array
     * @Serializer\Type("array<string>")
     */
    private $referenceTo = [];

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $relationshipName;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $relationshipOrder;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $restrictedDelete;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $restrictedPicklist;

    /**
     * @var int|null
     * @Serializer\Type("int")
     */
    private $scale;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $searchPrefilterable;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $soapType;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $sortable;

    /**
     * @var string|null
     * @Serializer\Type("bool")
     */
    private $type;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $unique;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $updateable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $writeRequiresMasterRead;

    /**
     * @return bool
     */
    public function isAggregatable(): bool
    {
        return $this->aggregatable;
    }

    /**
     * @param bool $aggregatable
     *
     * @return Field
     */
    public function setAggregatable(bool $aggregatable): Field
    {
        $this->aggregatable = $aggregatable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAiPredictionField(): bool
    {
        return $this->aiPredictionField;
    }

    /**
     * @param bool $aiPredictionField
     *
     * @return Field
     */
    public function setAiPredictionField(bool $aiPredictionField): Field
    {
        $this->aiPredictionField = $aiPredictionField;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAutoNumber(): bool
    {
        return $this->autoNumber;
    }

    /**
     * @param bool $autoNumber
     *
     * @return Field
     */
    public function setAutoNumber(bool $autoNumber): Field
    {
        $this->autoNumber = $autoNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getByteLength(): int
    {
        return $this->byteLength;
    }

    /**
     * @param int $byteLength
     *
     * @return Field
     */
    public function setByteLength(int $byteLength): Field
    {
        $this->byteLength = $byteLength;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCalculated(): bool
    {
        return $this->calculated;
    }

    /**
     * @param bool $calculated
     *
     * @return Field
     */
    public function setCalculated(bool $calculated): Field
    {
        $this->calculated = $calculated;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCalculatedFormula(): ?string
    {
        return $this->calculatedFormula;
    }

    /**
     * @param null|string $calculatedFormula
     *
     * @return Field
     */
    public function setCalculatedFormula(?string $calculatedFormula): Field
    {
        $this->calculatedFormula = $calculatedFormula;

        return $this;
    }

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
     * @return Field
     */
    public function setCascadeDelete(bool $cascadeDelete): Field
    {
        $this->cascadeDelete = $cascadeDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    /**
     * @param bool $caseSensitive
     *
     * @return Field
     */
    public function setCaseSensitive(bool $caseSensitive): Field
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCompoundFieldName(): ?string
    {
        return $this->compoundFieldName;
    }

    /**
     * @param null|string $compoundFieldName
     *
     * @return Field
     */
    public function setCompoundFieldName(?string $compoundFieldName): Field
    {
        $this->compoundFieldName = $compoundFieldName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    /**
     * @param null|string $controllerName
     *
     * @return Field
     */
    public function setControllerName(?string $controllerName): Field
    {
        $this->controllerName = $controllerName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCreateable(): bool
    {
        return $this->createable;
    }

    /**
     * @param bool $createable
     *
     * @return Field
     */
    public function setCreateable(bool $createable): Field
    {
        $this->createable = $createable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCustom(): bool
    {
        return $this->custom;
    }

    /**
     * @param bool $custom
     *
     * @return Field
     */
    public function setCustom(bool $custom): Field
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    /**
     * @param null|string $defaultValue
     *
     * @return Field
     */
    public function setDefaultValue(?string $defaultValue): Field
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDefaultValueFormula(): ?string
    {
        return $this->defaultValueFormula;
    }

    /**
     * @param null|string $defaultValueFormula
     *
     * @return Field
     */
    public function setDefaultValueFormula(?string $defaultValueFormula): Field
    {
        $this->defaultValueFormula = $defaultValueFormula;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultedOnCreate(): bool
    {
        return $this->defaultedOnCreate;
    }

    /**
     * @param bool $defaultedOnCreate
     *
     * @return Field
     */
    public function setDefaultedOnCreate(bool $defaultedOnCreate): Field
    {
        $this->defaultedOnCreate = $defaultedOnCreate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDependentPicklist(): bool
    {
        return $this->dependentPicklist;
    }

    /**
     * @param bool $dependentPicklist
     *
     * @return Field
     */
    public function setDependentPicklist(bool $dependentPicklist): Field
    {
        $this->dependentPicklist = $dependentPicklist;

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
     * @return Field
     */
    public function setDeprecatedAndHidden(bool $deprecatedAndHidden): Field
    {
        $this->deprecatedAndHidden = $deprecatedAndHidden;

        return $this;
    }

    /**
     * @return int
     */
    public function getDigits(): int
    {
        return $this->digits;
    }

    /**
     * @param int $digits
     *
     * @return Field
     */
    public function setDigits(int $digits): Field
    {
        $this->digits = $digits;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayLocationInDecimal(): bool
    {
        return $this->displayLocationInDecimal;
    }

    /**
     * @param bool $displayLocationInDecimal
     *
     * @return Field
     */
    public function setDisplayLocationInDecimal(bool $displayLocationInDecimal): Field
    {
        $this->displayLocationInDecimal = $displayLocationInDecimal;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->encrypted;
    }

    /**
     * @param bool $encrypted
     *
     * @return Field
     */
    public function setEncrypted(bool $encrypted): Field
    {
        $this->encrypted = $encrypted;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @param null|string $externalId
     *
     * @return Field
     */
    public function setExternalId(?string $externalId): Field
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExtraTypeInfo(): ?string
    {
        return $this->extraTypeInfo;
    }

    /**
     * @param null|string $extraTypeInfo
     *
     * @return Field
     */
    public function setExtraTypeInfo(?string $extraTypeInfo): Field
    {
        $this->extraTypeInfo = $extraTypeInfo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * @param bool $filterable
     *
     * @return Field
     */
    public function setFilterable(bool $filterable): Field
    {
        $this->filterable = $filterable;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFilterableLookupInfo(): ?string
    {
        return $this->filterableLookupInfo;
    }

    /**
     * @param null|string $filterableLookupInfo
     *
     * @return Field
     */
    public function setFilterableLookupInfo(?string $filterableLookupInfo): Field
    {
        $this->filterableLookupInfo = $filterableLookupInfo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForumlaTreatNullNumberAsZero(): bool
    {
        return $this->forumlaTreatNullNumberAsZero;
    }

    /**
     * @param bool $forumlaTreatNullNumberAsZero
     *
     * @return Field
     */
    public function setForumlaTreatNullNumberAsZero(bool $forumlaTreatNullNumberAsZero): Field
    {
        $this->forumlaTreatNullNumberAsZero = $forumlaTreatNullNumberAsZero;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGroupable(): bool
    {
        return $this->groupable;
    }

    /**
     * @param bool $groupable
     *
     * @return Field
     */
    public function setGroupable(bool $groupable): Field
    {
        $this->groupable = $groupable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHighScaleNumber(): bool
    {
        return $this->highScaleNumber;
    }

    /**
     * @param bool $highScaleNumber
     *
     * @return Field
     */
    public function setHighScaleNumber(bool $highScaleNumber): Field
    {
        $this->highScaleNumber = $highScaleNumber;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHtmlFormatted(): bool
    {
        return $this->htmlFormatted;
    }

    /**
     * @param bool $htmlFormatted
     *
     * @return Field
     */
    public function setHtmlFormatted(bool $htmlFormatted): Field
    {
        $this->htmlFormatted = $htmlFormatted;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIdLookup(): bool
    {
        return $this->idLookup;
    }

    /**
     * @param bool $idLookup
     *
     * @return Field
     */
    public function setIdLookup(bool $idLookup): Field
    {
        $this->idLookup = $idLookup;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getInlineHelpText(): ?string
    {
        return $this->inlineHelpText;
    }

    /**
     * @param null|string $inlineHelpText
     *
     * @return Field
     */
    public function setInlineHelpText(?string $inlineHelpText): Field
    {
        $this->inlineHelpText = $inlineHelpText;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param null|string $label
     *
     * @return Field
     */
    public function setLabel(?string $label): Field
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @param int|null $length
     *
     * @return Field
     */
    public function setLength(?int $length): Field
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMask(): ?string
    {
        return $this->mask;
    }

    /**
     * @param null|string $mask
     *
     * @return Field
     */
    public function setMask(?string $mask): Field
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMaskType(): ?string
    {
        return $this->maskType;
    }

    /**
     * @param null|string $maskType
     *
     * @return Field
     */
    public function setMaskType(?string $maskType): Field
    {
        $this->maskType = $maskType;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return Field
     */
    public function setName(?string $name): Field
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNameField(): bool
    {
        return $this->nameField;
    }

    /**
     * @param bool $nameField
     *
     * @return Field
     */
    public function setNameField(bool $nameField): Field
    {
        $this->nameField = $nameField;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNamePointing(): bool
    {
        return $this->namePointing;
    }

    /**
     * @param bool $namePointing
     *
     * @return Field
     */
    public function setNamePointing(bool $namePointing): Field
    {
        $this->namePointing = $namePointing;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNillable(): bool
    {
        return $this->nillable;
    }

    /**
     * @param bool $nillable
     *
     * @return Field
     */
    public function setNillable(bool $nillable): Field
    {
        $this->nillable = $nillable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermissionable(): bool
    {
        return $this->permissionable;
    }

    /**
     * @param bool $permissionable
     *
     * @return Field
     */
    public function setPermissionable(bool $permissionable): Field
    {
        $this->permissionable = $permissionable;

        return $this;
    }

    /**
     * @return PickListValue[]|array
     */
    public function getPicklistValues()
    {
        return $this->picklistValues;
    }

    /**
     * @param PickListValue[]|array $picklistValues
     *
     * @return Field
     */
    public function setPicklistValues($picklistValues)
    {
        $this->picklistValues = $picklistValues;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPolymorphicForeignKey(): bool
    {
        return $this->polymorphicForeignKey;
    }

    /**
     * @param bool $polymorphicForeignKey
     *
     * @return Field
     */
    public function setPolymorphicForeignKey(bool $polymorphicForeignKey): Field
    {
        $this->polymorphicForeignKey = $polymorphicForeignKey;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    /**
     * @param int|null $precision
     *
     * @return Field
     */
    public function setPrecision(?int $precision): Field
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * @return bool
     */
    public function isQueryByDistance(): bool
    {
        return $this->queryByDistance;
    }

    /**
     * @param bool $queryByDistance
     *
     * @return Field
     */
    public function setQueryByDistance(bool $queryByDistance): Field
    {
        $this->queryByDistance = $queryByDistance;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getReferenceTargetField(): ?string
    {
        return $this->referenceTargetField;
    }

    /**
     * @param null|string $referenceTargetField
     *
     * @return Field
     */
    public function setReferenceTargetField(?string $referenceTargetField): Field
    {
        $this->referenceTargetField = $referenceTargetField;

        return $this;
    }

    /**
     * @return array
     */
    public function getReferenceTo(): array
    {
        return $this->referenceTo;
    }

    /**
     * @param array $referenceTo
     *
     * @return Field
     */
    public function setReferenceTo(array $referenceTo): Field
    {
        $this->referenceTo = $referenceTo;

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
     * @return Field
     */
    public function setRelationshipName(?string $relationshipName): Field
    {
        $this->relationshipName = $relationshipName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRelationshipOrder(): ?string
    {
        return $this->relationshipOrder;
    }

    /**
     * @param null|string $relationshipOrder
     *
     * @return Field
     */
    public function setRelationshipOrder(?string $relationshipOrder): Field
    {
        $this->relationshipOrder = $relationshipOrder;

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
     * @return Field
     */
    public function setRestrictedDelete(bool $restrictedDelete): Field
    {
        $this->restrictedDelete = $restrictedDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRestrictedPicklist(): bool
    {
        return $this->restrictedPicklist;
    }

    /**
     * @param bool $restrictedPicklist
     *
     * @return Field
     */
    public function setRestrictedPicklist(bool $restrictedPicklist): Field
    {
        $this->restrictedPicklist = $restrictedPicklist;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getScale(): ?int
    {
        return $this->scale;
    }

    /**
     * @param int|null $scale
     *
     * @return Field
     */
    public function setScale(?int $scale): Field
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSearchPrefilterable(): bool
    {
        return $this->searchPrefilterable;
    }

    /**
     * @param bool $searchPrefilterable
     *
     * @return Field
     */
    public function setSearchPrefilterable(bool $searchPrefilterable): Field
    {
        $this->searchPrefilterable = $searchPrefilterable;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSoapType(): ?string
    {
        return $this->soapType;
    }

    /**
     * @param null|string $soapType
     *
     * @return Field
     */
    public function setSoapType(?string $soapType): Field
    {
        $this->soapType = $soapType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * @param bool $sortable
     *
     * @return Field
     */
    public function setSortable(bool $sortable): Field
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     *
     * @return Field
     */
    public function setType(?string $type): Field
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     *
     * @return Field
     */
    public function setUnique(bool $unique): Field
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUpdateable(): bool
    {
        return $this->updateable;
    }

    /**
     * @param bool $updateable
     *
     * @return Field
     */
    public function setUpdateable(bool $updateable): Field
    {
        $this->updateable = $updateable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isWriteRequiresMasterRead(): bool
    {
        return $this->writeRequiresMasterRead;
    }

    /**
     * @param bool $writeRequiresMasterRead
     *
     * @return Field
     */
    public function setWriteRequiresMasterRead(bool $writeRequiresMasterRead): Field
    {
        $this->writeRequiresMasterRead = $writeRequiresMasterRead;

        return $this;
    }
}
