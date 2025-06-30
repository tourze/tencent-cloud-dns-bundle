<?php

namespace TencentCloudDnsBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Exception\DnsServiceException;

class DnsServiceExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new DnsServiceException('Test message');
        
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }
}