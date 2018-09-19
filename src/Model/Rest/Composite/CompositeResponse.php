<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 10:00 AM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite;

use JMS\Serializer\Annotation as Serializer;

class CompositeResponse
{
    /**
     * @var array|SubRequestResult[]
     * @Serializer\Type("array<AE\SalesforceRestSdk\Model\Rest\Composite\SubRequestResult>")
     */
    private $compositeResponse = [];

    /**
     * @return SubRequestResult[]|array
     */
    public function getCompositeResponse()
    {
        return $this->compositeResponse;
    }

    /**
     * @param SubRequestResult[]|array $compositeResponse
     *
     * @return CompositeResponse
     */
    public function setCompositeResponse($compositeResponse)
    {
        $this->compositeResponse = $compositeResponse;

        return $this;
    }
}
