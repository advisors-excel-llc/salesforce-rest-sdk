<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 1:30 PM
 */

namespace AE\SalesforceRestSdk\Serializer;

use AE\SalesforceRestSdk\Model\Rest\Composite\CompositeCollection;
use AE\SalesforceRestSdk\Model\SObject;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;

class SObjectHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => SObject::class,
                'format' => 'json',
                'method' => 'serializeSObjectTojson'
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => SObject::class,
                'format' => 'json',
                'method' => 'deserializeSObjectFromjson'
            ]
        ];
    }

    public function serializeSObjectTojson(
        JsonSerializationVisitor $visitor,
        SObject $sobject,
        array $type,
        Context $context
    ): array {
        $object = [];

        foreach ($sobject->getFields() as $field => $value) {
            if (null === $value) {
                continue;
            }
            if (is_object($value)) {
                $class = get_class($value);
                $classMetadata = new ClassMetadata($class);
                $visitor->startVisitingObject(
                    $classMetadata,
                    $value,
                    []
                );
                switch ($class) {
                    case \DateTime::class:
                    case \DateTimeImmutable::class:
                        $dateMeta = new StaticPropertyMetadata(\DateTime::class, $field, $value);
                        $dateMeta->setType(['name' => 'DateTime', 'params' => [\DATE_ISO8601, 'UTC']]);
                        $visitor->visitProperty($dateMeta, $value);
                        break;
                    default:
                        $visitor->visitProperty(
                            new StaticPropertyMetadata($class, $field, $value),
                            $value
                        );
                        break;
                }
                $resultArray = $visitor->endVisitingObject(
                    $classMetadata,
                    $value,
                    []
                );
                $object[$field] = array_pop($resultArray);
            } else {
                $object[$field] = $value;
            }
        }

        return $object;
    }

    public function deserializeSObjectFromjson(
        JsonDeserializationVisitor $visitor,
        array $data,
        array $type,
        DeserializationContext $context
    ) {
        $sobject = new SObject();

        $metadata = $context->getMetadataFactory()->getMetadataForClass(SObject::class);
        $visitor->startVisitingObject($metadata, $sobject, $type);

        foreach ($data as $field => $value) {
            if (is_string($value)
                && preg_match('/^\d{4}-\d{2}-\d{2}\T\d{2}:\d{2}:\d{2}(\.\d{4})?(\+\d{4}|\Z)$/', $value) != false) {
                $sobject->$field = $context->getNavigator()->accept(
                    $value,
                    ['name' => 'DateTime', 'params' => [\DATE_ISO8601, 'UTC']]
                );
            } else {
                $sobject->$field = $value;
            }
        }

        $visitor->endVisitingObject($metadata, $sobject, $type);

        return $sobject;
    }
}
