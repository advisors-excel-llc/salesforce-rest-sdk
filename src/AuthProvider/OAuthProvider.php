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
    public const GRANT_PASSWORD = "password";
    public const GRANT_CODE     = "authorization_code";

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
     * @var string|null
     */
    protected $refreshToken;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var null|string
     */
    protected $instanceUrl;

    /**
     * @var string
     */
    protected $grantType;

    /**
     * @var string|null
     */
    protected $redirectUri;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var string|null
     */
    protected $identityUrl;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $url,
        ?string $username,
        ?string $password,
        string $grantType = self::GRANT_PASSWORD,
        ?string $redirectUri = null,
        ?string $code = null
    ) {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username     = $username;
        $this->password     = $password;
        $this->grantType    = $grantType;
        $this->redirectUri  = $redirectUri;
        $this->code         = $code;

        if (self::GRANT_PASSWORD === $this->grantType && (null === $this->username || null === $this->password)) {
            throw new \InvalidArgumentException(
                "A username and password must be provided when using the ".self::GRANT_PASSWORD." grant type."
            );
        }

        if (self::GRANT_CODE === $this->grantType && null === $this->redirectUri) {
            throw new \InvalidArgumentException(
                "A redirect URI is required when using the ".self::GRANT_CODE." grant type."
            );
        }

        if (self::GRANT_CODE === $this->grantType && null === $this->code) {
            throw new \InvalidArgumentException(
                "The authorization code from Salesforce is required when using ".self::GRANT_CODE." grant type."
            );
        }

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

        if (self::GRANT_PASSWORD === $this->grantType) {
            $response = $this->httpClient->post(
                '/services/oauth2/token',
                [
                    'form_params' => [
                        'grant_type'    => self::GRANT_PASSWORD,
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
        } elseif (null !== $this->refreshToken) {
            $response = $this->httpClient->post(
                '/services/oauth2/token',
                [
                    'form_params' => [
                        'grant_type'    => 'refresh_token',
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'refresh_token' => $this->refreshToken,
                    ],
                    'headers'     => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Accept'       => 'application/json',
                    ],
                ]
            );
        } else {
            $response = $this->httpClient->post(
                '/services/oauth2/token',
                [
                    'form_params' => [
                        'grant_type'    => self::GRANT_CODE,
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'redirect_uri'  => $this->redirectUri,
                        'code'          => $this->code,
                    ],
                    'headers'     => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Accept'       => 'application/json',
                    ],
                ]
            );
        }

        $body  = (string)$response->getBody();
        $parts = json_decode($body, true);

        if (401 === $response->getStatusCode()) {
            throw new SessionExpiredOrInvalidException($parts['message'], $parts['errorCode']);
        }

        $this->tokenType    = isset($parts['token_type']) ? $parts['token_type'] : $this->tokenType ?? 'Bearer';
        $this->token        = $parts['access_token'];
        $this->instanceUrl  = $parts['instance_url'];
        $this->refreshToken = isset($parts['refresh_token']) ? $parts['refresh_token'] : $this->refreshToken;
        $this->identityUrl  = $parts['id'];

        // Gotta get the username from the payload when using the authorization_code grant type
        if (!$this->isAuthorized && self::GRANT_CODE === $this->grantType) {
            $identity = $this->getIdentity();

            if (array_key_exists('username', $identity)) {
                $this->username = $identity['username'];
            }
        }

        $this->isAuthorized = true;

        return "{$this->tokenType} {$this->token}";
    }

    /**
     * @return string
     * @throws SessionExpiredOrInvalidException
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
        $this->refreshToken = null;
        $this->code         = null;
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
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
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

    /**
     * @return null|string
     */
    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    /**
     * @return null|string
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getGrantType(): string
    {
        return $this->grantType;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param null|string $code
     *
     * @return OAuthProvider
     */
    public function setCode(?string $code): OAuthProvider
    {
        $this->code = $code;

        return $this;
    }
}
