<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 11:40 AM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Psr\Cache\InvalidArgumentException;

class CachedOAuthProvider extends OAuthProvider
{
    use LoggerAwareTrait;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    public function __construct(
        AdapterInterface $adapter,
        string $clientId,
        string $clientSecret,
        string $url,
        ?string $username,
        ?string $password,
        string $grantType = self::GRANT_PASSWORD,
        ?string $redirectUri = null,
        ?string $code = null
    ) {
        parent::__construct($clientId, $clientSecret, $url, $username, $password, $grantType, $redirectUri, $code);
        $this->adapter = $adapter;
        $this->logger  = new NullLogger();
    }

    public function authorize($reauth = false): string
    {
        $oldToken = null;

        try {
            if (!$reauth && null === $this->token && $this->adapter->hasItem($this->clientId)) {
                $values             = $this->adapter->getItem($this->clientId)->get();
                $this->token        = $oldToken = $values['token'];
                $this->tokenType    = $values['tokenType'];
                $this->refreshToken = $values['refreshToken'];
                $this->instanceUrl  = $values['instanceUrl'];
                $this->identityUrl  = $values['identityUrl'];
                $this->isAuthorized = $values['isAuthorized'];
            }
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
        }

        try {
            $header = parent::authorize($reauth);

            if ($this->token !== $oldToken) {
                $item = $this->adapter->getItem($this->clientId);
                $item->set(
                    [
                        'token'        => $this->token,
                        'tokenType'    => $this->tokenType,
                        'refreshToken' => $this->refreshToken,
                        'instanceUrl'  => $this->instanceUrl,
                        'identityUrl'  => $this->identityUrl,
                        'isAuthorized' => $this->isAuthorized,
                    ]
                );
                $this->adapter->save($item);
            }

            return $header;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->revoke();
        }

        return '';
    }

    public function revoke(): void
    {
        try {
            if ($this->adapter->hasItem($this->clientId)) {
                $this->adapter->deleteItem($this->clientId);
            }
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
        }

        parent::revoke();
    }
}
