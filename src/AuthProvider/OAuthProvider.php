<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 3:27 PM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

use GuzzleHttp\Client;

class OAuthProvider implements AuthProviderInterface
{
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
    private $tokenType;

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
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var null|string
     */
    private $instanceUrl;

    public function __construct(string $clientId, string $clientSecret, string $username, string $password, string $url)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username     = $username;
        $this->password     = $password;

        $this->httpClient = new Client(
            [
                'base_uri' => $url,
            ]
        );
    }

    /**
     * @param bool $reauth
     *
     * @throws SessionExpiredOrInvalidException
     * @return string
     */
    public function authorize($reauth = false): string
    {
        if (!$reauth && $this->isAuthorized && strlen($this->token) > 0) {
            return "{$this->tokenType} {$this->token}";
        }

        $response = $this->httpClient->post(
            '/services/oauth2/token',
            [
                'form_params' => [
                    'grant_type'    => 'password',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'username'      => $this->username,
                    'password'      => $this->password,
                ],
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept'       => 'application/json',
                ],
            ]
        );

        $body  = (string)$response->getBody();
        $parts = json_decode($body, true);

        if (401 === $response->getStatusCode()) {
            throw new SessionExpiredOrInvalidException($parts['message'], $parts['errorCode']);
        }

        $this->tokenType   = $parts['token_type'];
        $this->token       = $parts['access_token'];
        $this->instanceUrl = $parts['instance_url'];

        $this->isAuthorized = true;

        return "{$this->tokenType} {$this->token}";
    }

    /**
     * @return string
     */
    public function reauthorize(): string
    {
        return $this->authorize(true);
    }


    public function revoke(): void
    {
        $this->token        = null;
        $this->tokenType    = null;
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
