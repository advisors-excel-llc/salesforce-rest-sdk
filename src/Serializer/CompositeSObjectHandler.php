<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 6:13 PM
 */

namespace AE\SalesforceRestSdk\Serializer;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeCollection;
use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeSObject;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class CompositeSObjectHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'type' => CompositeSObject::class,
                'format' => 'json',
            ],
        ];
    }

    public function serializeCompositeSObjectTojson(
        JsonSerializationVisitor $visitor,
        CompositeSObject $sobject,
        array $type,
        Context $context
    ): array {
        $object = [
            'attributes' => $sobject->getAttributes(),
        ];

        foreach ($sobject->getFields() as $field => $value) {
            if (null === $value) {
                continue;
            }
            if ($value instanceof CompositeCollection) {
                $object[$field] = $visitor->getNavigator()->accept(
                    $value,
                    ['name' => CompositeCollection::class],
                    $context
                )
                ;
            } else {
                if (is_object($value)) {
                    $className = get_class($value);
                    if (false !== $className) {
                        if (\DateTime::class === $className) {
                            $object[$field] = $visitor->getNavigator()->accept(
                                $value,
                                ['name' => 'DateTime', 'params' => [\DATE_ISO8601, 'UTC']],
                                $context
                            )
                            ;
                        } elseif (\DateTimeImmutable::class === $className) {
                            $object[$field] = $visitor->getNavigator()->accept(
                                $value,
                                ['name' => 'DateTimeImmutable', 'params' => [\DATE_ISO8601, 'UTC']],
                                $context
                            )
                            ;
                        } else {
                            $object[$field] = $visitor->getNavigator()->accept(
                                $value,
                                ['name' => $className],
                                $context
                            )
                            ;
                        }
                    } else {
                        $object[$field] = $value;
                    }
                } else {
                    $object[$field] = $value;
                }
            }
        }

        if (null === $visitor->getRoot()) {
            $visitor->setRoot($object);
        } elseif (is_array($visitor->getRoot())) {
            $visitor->setData(null, $object);
        }

        return $object;
    }

    public function deserializeCompositeSObjectFromjson(
        JsonDeserializationVisitor $visitor,
        array $data,
        array $type,
        DeserializationContext $context
    ) {
        $sobject = new CompositeSObject();

        if (array_key_exists('attributes', $data)) {
            if (array_key_exists('type', $data['attributes'])) {
                $sobject->setType($data['attributes']['type']);
            }

            if (array_key_exists('url', $data['attributes'])) {
                $sobject->setUrl($data['attributes']['url']);
            }

            if (array_key_exists('referenceId', $data['attributes'])) {
                $sobject->setReferenceId($data['attributes']['referenceId']);
            }
        }

        foreach ($data as $field => $value) {
            if (is_array($value) && array_key_exists('hasErrors', $value) && array_key_exists('records', $value)
                && is_array($value['records'])) {
                $sobject->$field = $visitor->getNavigator()->accept(
                    $value,
                    ['name' => CompositeCollection::class],
                    $context
                )
                ;
            } else {
                $sobject->$field = $value;
            }
        }

        return $sobject;
    }
}
