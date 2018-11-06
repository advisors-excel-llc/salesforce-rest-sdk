<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 1:01 PM
 */

namespace AE\SalesforceRestSdk\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class SObject
 *
 * @package AE\SalesforceRestSdk\Model
 * @Serializer\ExclusionPolicy("NONE")
 */
class SObject
{
    /**
     * @var array
     * @Serializer\Type("array")
     */
    protected $fields = [];

    public function __construct(array $fields = [])
    {
        if (!empty($fields)) {
            foreach ($fields as $field => $value) {
                $this->$field = $value;
            }
        }
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected static function normalizeFieldName($name): string
    {
        return ucwords($name);
    }

    public function setFields(array $fields): SObject
    {
        foreach ($fields as $field => $value) {
            $this->$field = $value;
        }

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function __set($name, $value)
    {
        $name = self::normalizeFieldName($name);
        $this->fields[$name] = $value;
    }

    public function __get($name)
    {
        $name = self::normalizeFieldName($name);

        return array_key_exists($name, $this->fields) ? $this->fields[$name] : null;
    }

    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);

        if ("get" === $prefix) {
            $field = substr($name, 3);

            return $this->$field;
        }

        if ("set" === $prefix) {
            $field = substr($name, 3);

            $this->$field = array_shift($arguments);

            return $this;
        }
    }
}
