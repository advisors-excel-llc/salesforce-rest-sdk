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
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

class CompositeSObjectHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => CompositeSObject::class,
                'format' => 'json',
                'method' => 'serializeCompositeSObjectTojson'
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => CompositeSObject::class,
                'format' => 'json',
                'method' => 'deserializeCompositeSObjectFromjson'
            ]
        ];
    }

    public function serializeCompositeSObjectTojson(
        JsonSerializationVisitor $visitor,
        CompositeSObject $sobject,
        array $type,
        SerializationContext $context
    ): array {
        $object = [
            'attributes' => $sobject->getAttributes(),
        ];

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
                    case CompositeCollection::class:
                        $compositeMeta = new PropertyMetadata(CompositeCollection::class, $field);
                        $compositeMeta->setType(['name' => CompositeCollection::class]);
                        $visitor->visitProperty($compositeMeta, $value);
                        break;
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

    public function deserializeCompositeSObjectFromjson(
        JsonDeserializationVisitor $visitor,
        array $data,
        array $type,
        DeserializationContext $context
    ) {
        $sobject = new CompositeSObject();

        $metadata = $context->getMetadataFactory()->getMetadataForClass(CompositeSObject::class);
        $visitor->startVisitingObject($metadata, $sobject, $type);

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
            if (strtolower($field) === 'attributes') {
                continue;
            }

            if (is_array($value) && array_key_exists('hasErrors', $value) && array_key_exists('records', $value)
                && is_array($value['records'])) {
                $sobject->$field = $context->getNavigator()->accept(
                    $value,
                    ['name' => CompositeCollection::class]
                )
                ;
            } elseif (is_string($value)
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
