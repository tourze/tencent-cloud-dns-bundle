<?php

namespace TencentCloudDnsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\TencentCloudDnsBundle;

class TencentCloudDnsBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new TencentCloudDnsBundle();
        $this->assertInstanceOf(TencentCloudDnsBundle::class, $bundle);
    }
}