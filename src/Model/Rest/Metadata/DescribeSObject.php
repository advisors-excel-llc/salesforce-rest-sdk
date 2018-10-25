<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 3:46 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Metadata;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class DescribeSObject
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Metadata
 * @Serializer\ExclusionPolicy("none")
 */
class DescribeSObject
{
    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $actionOverrides = [];

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $activateable;

    /**
     * @var array|ChildRelationship[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Metadata\ChildRelationship>")
     */
    private $childRelationships = [];

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $compactLayoutable;

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
     * @var bool
     * @Serializer\Type("bool")
     */
    private $customSetting;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $deletable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $deprecatedAndHidden;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $feedEnabled;

    /**
     * @var array|Field[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Metadata\Field>")
     */
    private $fields = [];

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $hasSubtypes;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $isSubtype;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $keyPrefix;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $label;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $labelPlural;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $layoutable;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $listviewable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $lookupLayoutable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $mergeable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $mruEnabled;

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $namedLayoutInfos = [];

    /**
     * @var string|null
     * @Serializer\Type("string")
     */
    private $networkScopeFieldName;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $queryable;

    /**
     * @var array|RecordTypeInfo[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Metadata\RecordTypeInfo>")
     */
    private $recordTypeInfos = [];

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $replicatateable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $retrieveable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $searchLayoutable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $searchable;

    /**
     * @var array|SupportedScope[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Metadata\SupportedScope>")
     */
    private $supportedScopes = [];

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $triggerable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $undeleteable;

    /**
     * @var bool
     * @Serializer\Type("bool")
     */
    private $updateable;

    /**
     * @var array|string[]
     * @Serializer\Type("array<string,string>")
     */
    private $urls = [];

    /**
     * @return array
     */
    public function getActionOverrides()
    {
        return $this->actionOverrides;
    }

    /**
     * @param array $actionOverrides
     *
     * @return DescribeSObject
     */
    public function setActionOverrides($actionOverrides)
    {
        $this->actionOverrides = $actionOverrides;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActivateable(): bool
    {
        return $this->activateable;
    }

    /**
     * @param bool $activateable
     *
     * @return DescribeSObject
     */
    public function setActivateable(bool $activateable): DescribeSObject
    {
        $this->activateable = $activateable;

        return $this;
    }

    /**
     * @return ChildRelationship[]|array
     */
    public function getChildRelationships()
    {
        return $this->childRelationships;
    }

    /**
     * @param ChildRelationship[]|array $childRelationships
     *
     * @return DescribeSObject
     */
    public function setChildRelationships($childRelationships)
    {
        $this->childRelationships = $childRelationships;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompactLayoutable(): bool
    {
        return $this->compactLayoutable;
    }

    /**
     * @param bool $compactLayoutable
     *
     * @return DescribeSObject
     */
    public function setCompactLayoutable(bool $compactLayoutable): DescribeSObject
    {
        $this->compactLayoutable = $compactLayoutable;

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
     * @return DescribeSObject
     */
    public function setCreateable(bool $createable): DescribeSObject
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
     * @return DescribeSObject
     */
    public function setCustom(bool $custom): DescribeSObject
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCustomSetting(): bool
    {
        return $this->customSetting;
    }

    /**
     * @param bool $customSetting
     *
     * @return DescribeSObject
     */
    public function setCustomSetting(bool $customSetting): DescribeSObject
    {
        $this->customSetting = $customSetting;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    /**
     * @param bool $deletable
     *
     * @return DescribeSObject
     */
    public function setDeletable(bool $deletable): DescribeSObject
    {
        $this->deletable = $deletable;

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
     * @return DescribeSObject
     */
    public function setDeprecatedAndHidden(bool $deprecatedAndHidden): DescribeSObject
    {
        $this->deprecatedAndHidden = $deprecatedAndHidden;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFeedEnabled(): bool
    {
        return $this->feedEnabled;
    }

    /**
     * @param bool $feedEnabled
     *
     * @return DescribeSObject
     */
    public function setFeedEnabled(bool $feedEnabled): DescribeSObject
    {
        $this->feedEnabled = $feedEnabled;

        return $this;
    }

    /**
     * @return Field[]|array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field[]|array $fields
     *
     * @return DescribeSObject
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasSubtypes(): bool
    {
        return $this->hasSubtypes;
    }

    /**
     * @param bool $hasSubtypes
     *
     * @return DescribeSObject
     */
    public function setHasSubtypes(bool $hasSubtypes): DescribeSObject
    {
        $this->hasSubtypes = $hasSubtypes;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSubtype(): bool
    {
        return $this->isSubtype;
    }

    /**
     * @param bool $isSubtype
     *
     * @return DescribeSObject
     */
    public function setIsSubtype(bool $isSubtype): DescribeSObject
    {
        $this->isSubtype = $isSubtype;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getKeyPrefix(): ?string
    {
        return $this->keyPrefix;
    }

    /**
     * @param null|string $keyPrefix
     *
     * @return DescribeSObject
     */
    public function setKeyPrefix(?string $keyPrefix): DescribeSObject
    {
        $this->keyPrefix = $keyPrefix;

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
     * @return DescribeSObject
     */
    public function setLabel(?string $label): DescribeSObject
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLabelPlural(): ?string
    {
        return $this->labelPlural;
    }

    /**
     * @param null|string $labelPlural
     *
     * @return DescribeSObject
     */
    public function setLabelPlural(?string $labelPlural): DescribeSObject
    {
        $this->labelPlural = $labelPlural;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLayoutable(): bool
    {
        return $this->layoutable;
    }

    /**
     * @param bool $layoutable
     *
     * @return DescribeSObject
     */
    public function setLayoutable(bool $layoutable): DescribeSObject
    {
        $this->layoutable = $layoutable;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getListviewable(): ?string
    {
        return $this->listviewable;
    }

    /**
     * @param null|string $listviewable
     *
     * @return DescribeSObject
     */
    public function setListviewable(?string $listviewable): DescribeSObject
    {
        $this->listviewable = $listviewable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLookupLayoutable(): bool
    {
        return $this->lookupLayoutable;
    }

    /**
     * @param bool $lookupLayoutable
     *
     * @return DescribeSObject
     */
    public function setLookupLayoutable(bool $lookupLayoutable): DescribeSObject
    {
        $this->lookupLayoutable = $lookupLayoutable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMergeable(): bool
    {
        return $this->mergeable;
    }

    /**
     * @param bool $mergeable
     *
     * @return DescribeSObject
     */
    public function setMergeable(bool $mergeable): DescribeSObject
    {
        $this->mergeable = $mergeable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMruEnabled(): bool
    {
        return $this->mruEnabled;
    }

    /**
     * @param bool $mruEnabled
     *
     * @return DescribeSObject
     */
    public function setMruEnabled(bool $mruEnabled): DescribeSObject
    {
        $this->mruEnabled = $mruEnabled;

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
     * @return DescribeSObject
     */
    public function setName(?string $name): DescribeSObject
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getNamedLayoutInfos(): array
    {
        return $this->namedLayoutInfos;
    }

    /**
     * @param array $namedLayoutInfos
     *
     * @return DescribeSObject
     */
    public function setNamedLayoutInfos(array $namedLayoutInfos): DescribeSObject
    {
        $this->namedLayoutInfos = $namedLayoutInfos;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNetworkScopeFieldName(): ?string
    {
        return $this->networkScopeFieldName;
    }

    /**
     * @param null|string $networkScopeFieldName
     *
     * @return DescribeSObject
     */
    public function setNetworkScopeFieldName(?string $networkScopeFieldName): DescribeSObject
    {
        $this->networkScopeFieldName = $networkScopeFieldName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isQueryable(): bool
    {
        return $this->queryable;
    }

    /**
     * @param bool $queryable
     *
     * @return DescribeSObject
     */
    public function setQueryable(bool $queryable): DescribeSObject
    {
        $this->queryable = $queryable;

        return $this;
    }

    /**
     * @return RecordTypeInfo[]|array
     */
    public function getRecordTypeInfos()
    {
        return $this->recordTypeInfos;
    }

    /**
     * @param RecordTypeInfo[]|array $recordTypeInfos
     *
     * @return DescribeSObject
     */
    public function setRecordTypeInfos($recordTypeInfos)
    {
        $this->recordTypeInfos = $recordTypeInfos;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReplicatateable(): bool
    {
        return $this->replicatateable;
    }

    /**
     * @param bool $replicatateable
     *
     * @return DescribeSObject
     */
    public function setReplicatateable(bool $replicatateable): DescribeSObject
    {
        $this->replicatateable = $replicatateable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRetrieveable(): bool
    {
        return $this->retrieveable;
    }

    /**
     * @param bool $retrieveable
     *
     * @return DescribeSObject
     */
    public function setRetrieveable(bool $retrieveable): DescribeSObject
    {
        $this->retrieveable = $retrieveable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSearchLayoutable(): bool
    {
        return $this->searchLayoutable;
    }

    /**
     * @param bool $searchLayoutable
     *
     * @return DescribeSObject
     */
    public function setSearchLayoutable(bool $searchLayoutable): DescribeSObject
    {
        $this->searchLayoutable = $searchLayoutable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     *
     * @return DescribeSObject
     */
    public function setSearchable(bool $searchable): DescribeSObject
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * @return SupportedScope[]|array
     */
    public function getSupportedScopes()
    {
        return $this->supportedScopes;
    }

    /**
     * @param SupportedScope[]|array $supportedScopes
     *
     * @return DescribeSObject
     */
    public function setSupportedScopes($supportedScopes)
    {
        $this->supportedScopes = $supportedScopes;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTriggerable(): bool
    {
        return $this->triggerable;
    }

    /**
     * @param bool $triggerable
     *
     * @return DescribeSObject
     */
    public function setTriggerable(bool $triggerable): DescribeSObject
    {
        $this->triggerable = $triggerable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUndeleteable(): bool
    {
        return $this->undeleteable;
    }

    /**
     * @param bool $undeleteable
     *
     * @return DescribeSObject
     */
    public function setUndeleteable(bool $undeleteable): DescribeSObject
    {
        $this->undeleteable = $undeleteable;

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
     * @return DescribeSObject
     */
    public function setUpdateable(bool $updateable): DescribeSObject
    {
        $this->updateable = $updateable;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @param array|string[] $urls
     *
     * @return DescribeSObject
     */
    public function setUrls($urls)
    {
        $this->urls = $urls;

        return $this;
    }
}
