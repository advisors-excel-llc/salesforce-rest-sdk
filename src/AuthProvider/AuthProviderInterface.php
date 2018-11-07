<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 9/6/18
 * Time: 3:28 PM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

interface AuthProviderInterface
{
    /**
     * @return mixed
     * @throws SessionExpiredOrInvalidException
     */
    public function authorize();
    public function reauthorize();
    public function revoke();
    public function getIdentity(): array;
    public function getToken(): ?string;
    public function getTokenType(): ?string;
    public function isAuthorized(): bool;
    public function getInstanceUrl(): ?string;
}
