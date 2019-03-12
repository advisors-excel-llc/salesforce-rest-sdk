<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 11:57 AM
 */

namespace AE\SalesforceRestSdk\AuthProvider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Psr\Cache\InvalidArgumentException;

class CachedSoapProvider extends SoapProvider
{
    use LoggerAwareTrait;

    /**
     * @var CacheItemPoolInterface
     */
    private $adapter;

    private const CACHE_KEY = "SOAP_AUTH_";

    public function __construct(
        CacheItemPoolInterface $adapter,
        string $username,
        string $password,
        string $url = 'https://login.salesforce.com/'
    ) {
        parent::__construct($username, $password, $url);
        $this->adapter = $adapter;
        $this->logger  = new NullLogger();
    }

    /**
     * @return string
     */
    private function getCacheKey(): string
    {
        return self::CACHE_KEY.preg_replace('/[\{\}\(\)\/\@]+/', '_', $this->username);
    }

    public function authorize($reauth = false)
    {
        $key      = $this->getCacheKey();
        $oldToken = null;

        try {
            if (!$reauth && null === $this->token && $this->adapter->hasItem($key)) {
                $values             = $this->adapter->getItem($key)->get();
                $this->token        = $oldToken = $values['token'];
                $this->tokenType    = $values['tokenType'];
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
                $item = $this->adapter->getItem($key);
                $item->set(
                    [
                        'token'        => $this->token,
                        'tokenType'    => $this->tokenType,
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
        $key = $this->getCacheKey();
        try {
            if ($this->adapter->hasItem($key)) {
                $this->adapter->deleteItem($key);
            }
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
        }

        parent::revoke();
    }
}
