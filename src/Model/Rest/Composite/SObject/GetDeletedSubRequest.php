<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/19/18
 * Time: 4:49 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest\Composite\SObject;

use AE\SalesforceRestSdk\Model\Rest\Composite\GetSubRequest;
use AE\SalesforceRestSdk\Model\Rest\Composite\SubRequest;
use AE\SalesforceRestSdk\Model\Rest\DeletedResponse;
use AE\SalesforceRestSdk\Rest\SObject\Client;
use JMS\Serializer\Annotation as Serializer;

class GetDeletedSubRequest extends GetSubRequest implements GetDeletedSubRequestInterface
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

    public function __construct(string $sObjectType, \DateTime $start, ?\DateTime $end, ?string $referenceId = null)
    {
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
     *
     * @return GetDeletedSubRequest
     */
    public function setStart(\DateTime $start): GetDeletedSubRequest
    {
        $this->start = $start;

        return $this;
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
     *
     * @return GetDeletedSubRequest
     */
    public function setEnd(\DateTime $end): GetDeletedSubRequest
    {
        $this->end = $end;

        return $this;
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
        return DeletedResponse::class;
    }

    /**
     * @Serializer\PreSerialize()
     */
    public function preSerialize()
    {
        $this->start->setTimezone(new \DateTimeZone("UTC"));
        $this->end->setTimezone(new \DateTimeZone("UTC"));

        $this->url = '/'.Client::BASE_PATH.'sobjects/'.$this->sObjectType.'/deleted/?'
            .http_build_query(
                [
                    'start' => $this->start->format('Y-m-d\Th:i:sP'),
                    'end'   => $this->end->format('Y-m-d\Th:i:sP'),
                ],
                null,
                '&',
                PHP_QUERY_RFC3986
            );
    }
}
