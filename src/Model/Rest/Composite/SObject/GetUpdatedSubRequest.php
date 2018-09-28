<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 4:43 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\UpdatedResponse;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class GetUpdatedSubRequest extends GetSubRequest implements GetUpdatedSubRequestInterface
{
    /**
     * @var string
     * @Serializer\Exclude()
     */
    private $sObjectType;

    /**
     * @var \DateTime
     * @Serializer\Exclude()
     */
    private $start;

    /**
     * @var \DateTime
     * @Serializer\Exclude()
     */
    private $end;

    public function __construct(
        string $sObjectType,
        \DateTime $start,
        ?\DateTime $end = null,
        ?string $referenceId = null
    ) {
        parent::__construct($referenceId);

        $this->sObjectType = $sObjectType;
        $this->start       = $start;
        $this->end         = $end ?: new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start): void
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end): void
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getSObjectType(): string
    {
        return $this->sObjectType;
    }

    final public function setBody($body): SubRequest
    {
        return $this;
    }

    final public function setUrl(string $url): SubRequest
    {
        return $this;
    }

    public function getResultClass(): ?string
    {
        return UpdatedResponse::class;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        $this->start->setTimezone(new \DateTimeZone("UTC"));
        $this->end->setTimezone(new \DateTimeZone("UTC"));

        $this->url = '/'.Client::BASE_PATH.'sobjects/'.$this->sObjectType.'/updated/?'
            .http_build_query(
                [
                    'start' => $this->start->format(\DATE_ISO8601),
                    'end'   => $this->end->format(\DATE_ISO8601),
                ]
            );
    }
}
