<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/13/18
 * Time: 5:29 PM
 */

namespace AE\SalesforceRestSdk\Rest\Composite;

use AE\SalesforceRestSdk\Model\Rest\CreateResponse;
use JMS\Serializer\Annotation as Serializer;

class CompositeResponse extends CreateResponse implements CompositeResponseInterface
{
    /**
     * @var array|null
     * @Serializer\Type("array")
     */
    private $warnings;

    /**
     * @return array|null
     */
    public function getWarnings(): ?array
    {
        return $this->warnings;
    }

    /**
     * @param array|null $warnings
     *
     * @return CompositeResponse
     */
    public function setWarnings(?array $warnings): CompositeResponse
    {
        $this->warnings = $warnings;

        return $this;
    }
}
