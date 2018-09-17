<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/17/18
 * Time: 3:47 PM
 */

namespace AE\SalesforceRestSdk\Model\Rest;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class Limits
 *
 * @package AE\SalesforceRestSdk\Model\Rest
 * @Serializer\ExclusionPolicy("none")
 */
class Limits
{
    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $ConcurrentAsyncGetReportInstances;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $ConcurrentSyncReportRuns;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyApiRequests;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyAsyncApexExecutions;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyBulkApiRequests;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyDurableGenericStreamingApiEvents;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyDurableStreamingApiEvents;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyGenericStreamingApiEvents;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyStreamingApiEvents;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DailyWorkflowEmails;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DataStorageMB;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $DurableStreamingApiConcurrentClients;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $FileStorageMB;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlyAsyncReportRuns;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlyDashboardRefreshes;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlyDashboardResults;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlyDashboardStatuses;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlyODataCallout;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlySyncReportRuns;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $HourlyTimeBasedWorkflow;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $MassEmail;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $SingleEmail;

    /**
     * @var Limit
     * @Serializer\Type("AE\SalesforceRestSdk\Model\Rest\Limit")
     */
    private $StreamingApiConcurrentClients;

    /**
     * Limits constructor.
     */
    public function __construct()
    {
        $this->ConcurrentAsyncGetReportInstances     = new Limit();
        $this->ConcurrentSyncReportRuns              = new Limit();
        $this->DailyApiRequests                      = new Limit();
        $this->DailyAsyncApexExecutions              = new Limit();
        $this->DailyBulkApiRequests                  = new Limit();
        $this->DailyDurableGenericStreamingApiEvents = new Limit();
        $this->DailyDurableStreamingApiEvents        = new Limit();
        $this->DailyStreamingApiEvents               = new Limit();
        $this->DailyGenericStreamingApiEvents        = new Limit();
        $this->DailyWorkflowEmails                   = new Limit();
        $this->HourlyAsyncReportRuns                 = new Limit();
        $this->HourlyDashboardRefreshes              = new Limit();
        $this->HourlyDashboardResults               = new Limit();
        $this->HourlyDashboardStatuses              = new Limit();
        $this->HourlyODataCallout                    = new Limit();
        $this->HourlySyncReportRuns                  = new Limit();
        $this->HourlyTimeBasedWorkflow               = new Limit();
        $this->DurableStreamingApiConcurrentClients  = new Limit();
        $this->FileStorageMB                         = new Limit();
        $this->DataStorageMB                         = new Limit();
        $this->MassEmail                             = new Limit();
        $this->SingleEmail                           = new Limit();
        $this->StreamingApiConcurrentClients         = new Limit();
    }

    /**
     * @return Limit
     */
    public function getConcurrentAsyncGetReportInstances(): Limit
    {
        return $this->ConcurrentAsyncGetReportInstances;
    }

    /**
     * @param Limit $ConcurrentAsyncGetReportInstances
     *
     * @return Limits
     */
    public function setConcurrentAsyncGetReportInstances(Limit $ConcurrentAsyncGetReportInstances): Limits
    {
        $this->ConcurrentAsyncGetReportInstances = $ConcurrentAsyncGetReportInstances;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getConcurrentSyncReportRuns(): Limit
    {
        return $this->ConcurrentSyncReportRuns;
    }

    /**
     * @param Limit $ConcurrentSyncReportRuns
     *
     * @return Limits
     */
    public function setConcurrentSyncReportRuns(Limit $ConcurrentSyncReportRuns): Limits
    {
        $this->ConcurrentSyncReportRuns = $ConcurrentSyncReportRuns;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyApiRequests(): Limit
    {
        return $this->DailyApiRequests;
    }

    /**
     * @param Limit $DailyApiRequests
     *
     * @return Limits
     */
    public function setDailyApiRequests(Limit $DailyApiRequests): Limits
    {
        $this->DailyApiRequests = $DailyApiRequests;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyAsyncApexExecutions(): Limit
    {
        return $this->DailyAsyncApexExecutions;
    }

    /**
     * @param Limit $DailyAsyncApexExecutions
     *
     * @return Limits
     */
    public function setDailyAsyncApexExecutions(Limit $DailyAsyncApexExecutions): Limits
    {
        $this->DailyAsyncApexExecutions = $DailyAsyncApexExecutions;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyBulkApiRequests(): Limit
    {
        return $this->DailyBulkApiRequests;
    }

    /**
     * @param Limit $DailyBulkApiRequests
     *
     * @return Limits
     */
    public function setDailyBulkApiRequests(Limit $DailyBulkApiRequests): Limits
    {
        $this->DailyBulkApiRequests = $DailyBulkApiRequests;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyDurableGenericStreamingApiEvents(): Limit
    {
        return $this->DailyDurableGenericStreamingApiEvents;
    }

    /**
     * @param Limit $DailyDurableGenericStreamingApiEvents
     *
     * @return Limits
     */
    public function setDailyDurableGenericStreamingApiEvents(Limit $DailyDurableGenericStreamingApiEvents): Limits
    {
        $this->DailyDurableGenericStreamingApiEvents = $DailyDurableGenericStreamingApiEvents;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyDurableStreamingApiEvents(): Limit
    {
        return $this->DailyDurableStreamingApiEvents;
    }

    /**
     * @param Limit $DailyDurableStreamingApiEvents
     *
     * @return Limits
     */
    public function setDailyDurableStreamingApiEvents(Limit $DailyDurableStreamingApiEvents): Limits
    {
        $this->DailyDurableStreamingApiEvents = $DailyDurableStreamingApiEvents;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyGenericStreamingApiEvents(): Limit
    {
        return $this->DailyGenericStreamingApiEvents;
    }

    /**
     * @param Limit $DailyGenericStreamingApiEvents
     *
     * @return Limits
     */
    public function setDailyGenericStreamingApiEvents(Limit $DailyGenericStreamingApiEvents): Limits
    {
        $this->DailyGenericStreamingApiEvents = $DailyGenericStreamingApiEvents;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyStreamingApiEvents(): Limit
    {
        return $this->DailyStreamingApiEvents;
    }

    /**
     * @param Limit $DailyStreamingApiEvents
     *
     * @return Limits
     */
    public function setDailyStreamingApiEvents(Limit $DailyStreamingApiEvents): Limits
    {
        $this->DailyStreamingApiEvents = $DailyStreamingApiEvents;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDailyWorkflowEmails(): Limit
    {
        return $this->DailyWorkflowEmails;
    }

    /**
     * @param Limit $DailyWorkflowEmails
     *
     * @return Limits
     */
    public function setDailyWorkflowEmails(Limit $DailyWorkflowEmails): Limits
    {
        $this->DailyWorkflowEmails = $DailyWorkflowEmails;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDataStorageMB(): Limit
    {
        return $this->DataStorageMB;
    }

    /**
     * @param Limit $DataStorageMB
     *
     * @return Limits
     */
    public function setDataStorageMB(Limit $DataStorageMB): Limits
    {
        $this->DataStorageMB = $DataStorageMB;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getDurableStreamingApiConcurrentClients(): Limit
    {
        return $this->DurableStreamingApiConcurrentClients;
    }

    /**
     * @param Limit $DurableStreamingApiConcurrentClients
     *
     * @return Limits
     */
    public function setDurableStreamingApiConcurrentClients(Limit $DurableStreamingApiConcurrentClients): Limits
    {
        $this->DurableStreamingApiConcurrentClients = $DurableStreamingApiConcurrentClients;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getFileStorageMB(): Limit
    {
        return $this->FileStorageMB;
    }

    /**
     * @param Limit $FileStorageMB
     *
     * @return Limits
     */
    public function setFileStorageMB(Limit $FileStorageMB): Limits
    {
        $this->FileStorageMB = $FileStorageMB;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlyAsyncReportRuns(): Limit
    {
        return $this->HourlyAsyncReportRuns;
    }

    /**
     * @param Limit $HourlyAsyncReportRuns
     *
     * @return Limits
     */
    public function setHourlyAsyncReportRuns(Limit $HourlyAsyncReportRuns): Limits
    {
        $this->HourlyAsyncReportRuns = $HourlyAsyncReportRuns;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlyDashboardRefreshes(): Limit
    {
        return $this->HourlyDashboardRefreshes;
    }

    /**
     * @param Limit $HourlyDashboardRefreshes
     *
     * @return Limits
     */
    public function setHourlyDashboardRefreshes(Limit $HourlyDashboardRefreshes): Limits
    {
        $this->HourlyDashboardRefreshes = $HourlyDashboardRefreshes;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlyDashboardResults(): Limit
    {
        return $this->HourlyDashboardResults;
    }

    /**
     * @param Limit $HourlyDashboardResults
     *
     * @return Limits
     */
    public function setHourlyDashboardResults(Limit $HourlyDashboardResults): Limits
    {
        $this->HourlyDashboardResults = $HourlyDashboardResults;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlyDashboardStatuses(): Limit
    {
        return $this->HourlyDashboardStatuses;
    }

    /**
     * @param Limit $HourlyDashboardStatuses
     *
     * @return Limits
     */
    public function setHourlyDashboardStatuses(Limit $HourlyDashboardStatuses): Limits
    {
        $this->HourlyDashboardStatuses = $HourlyDashboardStatuses;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlyODataCallout(): Limit
    {
        return $this->HourlyODataCallout;
    }

    /**
     * @param Limit $HourlyODataCallout
     *
     * @return Limits
     */
    public function setHourlyODataCallout(Limit $HourlyODataCallout): Limits
    {
        $this->HourlyODataCallout = $HourlyODataCallout;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlySyncReportRuns(): Limit
    {
        return $this->HourlySyncReportRuns;
    }

    /**
     * @param Limit $HourlySyncReportRuns
     *
     * @return Limits
     */
    public function setHourlySyncReportRuns(Limit $HourlySyncReportRuns): Limits
    {
        $this->HourlySyncReportRuns = $HourlySyncReportRuns;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getHourlyTimeBasedWorkflow(): Limit
    {
        return $this->HourlyTimeBasedWorkflow;
    }

    /**
     * @param Limit $HourlyTimeBasedWorkflow
     *
     * @return Limits
     */
    public function setHourlyTimeBasedWorkflow(Limit $HourlyTimeBasedWorkflow): Limits
    {
        $this->HourlyTimeBasedWorkflow = $HourlyTimeBasedWorkflow;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getMassEmail(): Limit
    {
        return $this->MassEmail;
    }

    /**
     * @param Limit $MassEmail
     *
     * @return Limits
     */
    public function setMassEmail(Limit $MassEmail): Limits
    {
        $this->MassEmail = $MassEmail;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getSingleEmail(): Limit
    {
        return $this->SingleEmail;
    }

    /**
     * @param Limit $SingleEmail
     *
     * @return Limits
     */
    public function setSingleEmail(Limit $SingleEmail): Limits
    {
        $this->SingleEmail = $SingleEmail;

        return $this;
    }

    /**
     * @return Limit
     */
    public function getStreamingApiConcurrentClients(): Limit
    {
        return $this->StreamingApiConcurrentClients;
    }

    /**
     * @param Limit $StreamingApiConcurrentClients
     *
     * @return Limits
     */
    public function setStreamingApiConcurrentClients(Limit $StreamingApiConcurrentClients): Limits
    {
        $this->StreamingApiConcurrentClients = $StreamingApiConcurrentClients;

        return $this;
    }
}
