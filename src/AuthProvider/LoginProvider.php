<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 3:27 PM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

use GuzzleHttp\Client;

class LoginProvider implements AuthProviderInterface
{
    /**
     * @var bool
     */
    private $isAuthorized = false;

    /**
     * @var String
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
     * @return string
     */
    public function authorize($reauth = false): string
    {
        if (!$reauth && ($this->isAuthorized && strlen($this->token) > 0)) {
            return "{$this->tokenType} {$this->token}";
        }

        $response = $this->httpClient->post(
            '/services/oauth2/token',
            [
                'form_params' => [
                    'grant_type'      => 'password',
                    'client_id'       => $this->clientId,
                    'client_secret'   => $this->clientSecret,
                    'username' => $this->username,
                    'password' => $this->password,
                ],
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept'       => 'application/json',
                ],
            ]
        );

        $body  = (string)$response->getBody();
        $parts = json_decode($body, true);

        $this->tokenType = $parts['token_type'];
        $this->token     = $parts['access_token'];

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
}
