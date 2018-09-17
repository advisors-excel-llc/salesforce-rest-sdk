<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 6:03 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class CompositeSObject
 *
 * @package AE\SalesforceRestSdk\Model\Rest\Composite
 * @Serializer\ExclusionPolicy("none")
 */
class CompositeSObject extends SObject
{
    /**
     * @var array
     * @Serializer\Type("array")
     */
    private $attributes = ['type' => 'sobject'];

    public function __construct(string $type = 'sobject', array $fields = [])
    {
        parent::__construct($fields);
        $this->attributes['type'] = $type;
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected static function normalizeFieldName($name): string
    {
        if ($name !== "id") {
            return parent::normalizeFieldName($name);
        }

        if ($name === 'Id') {
            return'id';
        }

        return $name;
    }

    public function getType(): string
    {
        return $this->attributes['type'];
    }

    public function setType(string $type): SObject
    {
        $this->attributes['type'] = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return array_key_exists('url', $this->attributes) ? $this->attributes['url'] : null;
    }

    public function setUrl(?string $url): SObject
    {
        $this->attributes['url'] = $url;

        return $this;
    }

    public function __set($name, $value)
    {
        if ('type' === $name) {
            $this->setType($value);
        } elseif ('url' === $name) {
            $this->setUrl($value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function __get($name)
    {
        if ('type' === $name) {
            return $this->getType();
        }

        if ('url' === $name) {
            return $this->getUrl();
        }

        return parent::__get($name);
    }
}
