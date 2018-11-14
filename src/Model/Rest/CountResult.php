<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 11/14/18
 * Time: 10:51 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;
use Traversable;

/**
 * Class CountResult
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 */
class CountResult implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var array|Count[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Count>")
     */
    private $sObjects = [];

    /**
     * @return array|Count[]
     */
    public function getSObjects(): array
    {
        return $this->sObjects;
    }

    /**
     * @param array|Count[] $sObjects
     *
     * @return CountResult
     */
    public function setSObjects(array $sObjects): CountResult
    {
        $this->sObjects = $sObjects;

        return $this;
    }

    public function offsetExists($offset)
    {
        return isset($this->sObjects[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->sObjects[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->sObjects[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->sObjects[$offset]);
    }

    public function current()
    {
        return current($this->sObjects);
    }

    public function next()
    {
        return next($this->sObjects);
    }

    public function key()
    {
        return key($this->sObjects);
    }

    public function valid()
    {
        return false !== current($this->sObjects);
    }

    public function rewind()
    {
        return reset($this->sObjects);
    }

    public function count()
    {
        return count($this->sObjects);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->sObjects);
    }
}
