<?php

namespace TencentCloudDnsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudDnsBundle\Exception\DnsServiceException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(DnsServiceException::class)]
final class DnsServiceExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new DnsServiceException('Test message');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }
}
