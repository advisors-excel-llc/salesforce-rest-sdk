<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 12:04 PM
 */

namespace AE\SalesforceRestSdk\Bayeux\Transport;

use GuzzleHttp\Client;

abstract class HttpClientTransport extends AbstractClientTransport
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     *
     * @return HttpClientTransport
     */
    public function setHttpClient(Client $httpClient): HttpClientTransport
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}
