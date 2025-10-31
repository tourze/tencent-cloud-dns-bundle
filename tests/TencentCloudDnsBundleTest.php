<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\TencentCloudDnsBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(TencentCloudDnsBundle::class)]
#[RunTestsInSeparateProcesses]
final class TencentCloudDnsBundleTest extends AbstractBundleTestCase
{
}
