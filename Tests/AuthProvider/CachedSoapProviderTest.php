<?php
/**
 * Created by PhpStorm.
 * User: alex.boyce
 * Date: 3/11/19
 * Time: 12:24 PM
 */

namespace AE\SalesforceRestSdk\Tests\AuthProvider;

use AE\SalesforceRestSdk\AuthProvider\CachedSoapProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CachedSoapProviderTest extends TestCase
{
    public function testAuthorize()
    {
        $adapter = new ArrayAdapter();
        $provider = new CachedSoapProvider(
            $adapter,
            getenv("SF_USER"),
            getenv("SF_PASS"),
            getenv("SF_LOGIN_URL")
        );

        $initHeader = $provider->authorize();
        $this->assertGreaterThan(0, strlen($initHeader));

        $provider2 = new CachedSoapProvider(
            $adapter,
            getenv("SF_USER"),
            getenv("SF_PASS"),
            getenv("SF_LOGIN_URL")
        );

        $nextHeader = $provider2->authorize();
        $this->assertEquals($initHeader, $nextHeader);
    }
}
