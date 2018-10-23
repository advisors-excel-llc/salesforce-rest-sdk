<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 10/23/18
 * Time: 9:08 AM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SoapProvider implements AuthProviderInterface
{
    public const VERSION = "44.0";
    /**
     * @var bool
     */
    private $isAuthorized = false;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $tokenType = 'Bearer';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var null|string
     */
    private $instanceUrl;

    public function __construct(string $username, string $password, string $url = 'https://login.salesforce.com/')
    {
        $this->username   = $username;
        $this->password   = $password;
        $this->httpClient = new Client(
            [
                'base_uri' => $url,
                'headers'  => [
                    'Content-Type' => 'text/xml',
                    'SOAPAction'   => '""',
                ],
            ]
        );
    }

    public function authorize($reauth = false)
    {
        if (!$reauth && $this->isAuthorized && strlen($this->token) > 0) {
            return "{$this->tokenType} {$this->token}";
        }

        $body
            = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\">
   <soapenv:Body>
      <login xmlns=\"urn:partner.soap.sforce.com\">
         <username>{$this->username}</username>
         <password>{$this->password}</password>
      </login>
   </soapenv:Body>
</soapenv:Envelope>";

        try {
            $response = $this->httpClient->post(
                '/services/Soap/u/'.self::VERSION,
                [
                    'body' => $body,
                ]
            );

            $soapBody = (string)$response->getBody();

            $matches  = [];
            if (false != preg_match(
                '/<serverUrl>(?<serverUrl>.*?)<\/serverUrl>.*?<sessionId>(?<sessionId>.*?)<\/sessionId>/',
                $soapBody,
                $matches
            )) {
                $host = parse_url($matches['serverUrl'], PHP_URL_HOST);
                $this->instanceUrl = "https://$host/";
                $this->token       = $matches['sessionId'];
            } else {
                throw new SessionExpiredOrInvalidException("Failed to login to Salesforce.", "INVALID_CREDENTIALS");
            }

            return "{$this->tokenType} {$this->token}";
        } catch (RequestException $e) {
            throw new SessionExpiredOrInvalidException(
                "Failed to authenticate with Salesforce.",
                "INVALID_CREDENTIALS"
            );
        }
    }

    public function reauthorize()
    {
        return $this->authorize(true);
    }

    public function revoke(): void
    {
        $this->token        = null;
        $this->isAuthorized = false;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->isAuthorized;
    }

    /**
     * @return null|string
     */
    public function getInstanceUrl(): ?string
    {
        return $this->instanceUrl;
    }
}
