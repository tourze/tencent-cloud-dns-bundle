<?php

namespace TencentCloudDnsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DnsRecordType::class)]
final class DnsRecordTypeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('A', DnsRecordType::A->value);
        $this->assertEquals('MX', DnsRecordType::MX->value);
        $this->assertEquals('TXT', DnsRecordType::TXT->value);
        $this->assertEquals('CNAME', DnsRecordType::CNAME->value);
        $this->assertEquals('NS', DnsRecordType::NS->value);
        $this->assertEquals('URI', DnsRecordType::URI->value);
    }

    public function testLabels(): void
    {
        $this->assertEquals('A记录', DnsRecordType::A->getLabel());
        $this->assertEquals('MX记录', DnsRecordType::MX->getLabel());
        $this->assertEquals('TXT记录', DnsRecordType::TXT->getLabel());
        $this->assertEquals('CNAME记录', DnsRecordType::CNAME->getLabel());
        $this->assertEquals('NS记录', DnsRecordType::NS->getLabel());
        $this->assertEquals('URI记录', DnsRecordType::URI->getLabel());
    }

    public function testAllEnum(): void
    {
        $cases = DnsRecordType::cases();

        $this->assertCount(6, $cases);
        $this->assertContains(DnsRecordType::A, $cases);
        $this->assertContains(DnsRecordType::MX, $cases);
        $this->assertContains(DnsRecordType::TXT, $cases);
        $this->assertContains(DnsRecordType::CNAME, $cases);
        $this->assertContains(DnsRecordType::NS, $cases);
        $this->assertContains(DnsRecordType::URI, $cases);
    }

    public function testFromMethod(): void
    {
        $this->assertSame(DnsRecordType::A, DnsRecordType::from('A'));
        $this->assertSame(DnsRecordType::MX, DnsRecordType::from('MX'));
        $this->assertSame(DnsRecordType::TXT, DnsRecordType::from('TXT'));
        $this->assertSame(DnsRecordType::CNAME, DnsRecordType::from('CNAME'));
        $this->assertSame(DnsRecordType::NS, DnsRecordType::from('NS'));
        $this->assertSame(DnsRecordType::URI, DnsRecordType::from('URI'));
    }

    public function testToArray(): void
    {
        $result = DnsRecordType::A->toArray();

        $this->assertCount(2, $result);

        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('label', $result);

        $this->assertEquals('A', $result['value']);
        $this->assertEquals('A记录', $result['label']);

        // 测试其他枚举值
        $mxResult = DnsRecordType::MX->toArray();
        $this->assertEquals('MX', $mxResult['value']);
        $this->assertEquals('MX记录', $mxResult['label']);
    }
}
