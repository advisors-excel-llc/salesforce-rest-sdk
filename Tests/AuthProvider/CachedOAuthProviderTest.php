<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 12:15 PM
 */

namespace AE\SalesforceRestSdk\Tests\AuthProvider;

use AE\SalesforceRestSdk\AuthProvider\CachedOAuthProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CachedOAuthProviderTest extends TestCase
{
    public function testAuthorize()
    {
        $adapter = new ArrayAdapter();
        $provider = new CachedOAuthProvider(
            $adapter,
            getenv("SF_CLIENT_ID"),
            getenv("SF_CLIENT_SECRET"),
            getenv("SF_LOGIN_URL"),
            getenv("SF_USER"),
            getenv("SF_PASS")
        );

        $initHeader = $provider->authorize();
        $this->assertGreaterThan(0, strlen($initHeader));

        $provider2 = new CachedOAuthProvider(
            $adapter,
            getenv("SF_CLIENT_ID"),
            getenv("SF_CLIENT_SECRET"),
            getenv("SF_LOGIN_URL"),
            getenv("SF_USER"),
            getenv("SF_PASS")
        );

        $nextHeader = $provider2->authorize();

        $this->assertEquals($initHeader, $nextHeader);
    }
}
