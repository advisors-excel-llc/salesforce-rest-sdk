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
    protected $isAuthorized = false;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $tokenType = 'Bearer';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var null|string
     */
    protected $instanceUrl;

    /**
     * @var null|string
     */
    protected $identityUrl;

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
                '/<serverUrl>(?<serverUrl>.*?)\/services.*?<\/serverUrl>.*?<sessionId>(?<sessionId>.*?)<\/sessionId>.*?'.
                '<userId>(?<userId>.*?)<\/userId>.*?<organizationId>(?<orgId>.*?)<\/organizationId>/',
                $soapBody,
                $matches
            )) {
                $this->instanceUrl = $matches['serverUrl'];
                $this->token       = $matches['sessionId'];
                $this->identityUrl = '/id/'.$matches['orgId'].'/'.$matches['userId'];
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
        $this->identityUrl  = null;
    }

    public function getIdentity(): array
    {
        if (null === $this->identityUrl) {
            return [];
        }

        $idRes = $this->httpClient->get(
            $this->identityUrl,
            [
                'headers' => ['Authorization' => "{$this->tokenType} {$this->token}"],
            ]
        );

        if (200 === $idRes->getStatusCode()) {
            $idBody = (string)$idRes->getBody();

            return json_decode($idBody, true);
        }

        return [];
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
