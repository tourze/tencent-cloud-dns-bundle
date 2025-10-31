<?php

namespace TencentCloudDnsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DnsRecord::class)]
final class DnsRecordTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new DnsRecord();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $domain = new DnsDomain();
        $domain->setName('example.com');

        yield 'domain' => ['domain', $domain];
        yield 'name' => ['name', 'test'];
        yield 'value' => ['value', '192.168.1.1'];
        yield 'recordId' => ['recordId', '12345'];
        yield 'type' => ['type', DnsRecordType::A];
        yield 'ttl' => ['ttl', 600];
        yield 'context' => ['context', ['key' => 'value']];
        yield 'valid' => ['valid', true];
        yield 'createdBy' => ['createdBy', 'admin'];
        yield 'updatedBy' => ['updatedBy', 'admin2'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    public function testGetterAndSettersManual(): void
    {
        $record = new DnsRecord();
        $domain = new DnsDomain();
        $domain->setName('example.com');

        // 测试 domain 属性
        $record->setDomain($domain);
        $this->assertSame($domain, $record->getDomain());

        // 测试 name 属性
        $name = 'test';
        $record->setName($name);
        $this->assertEquals($name, $record->getName());

        // 测试 value 属性
        $value = '192.168.1.1';
        $record->setValue($value);
        $this->assertEquals($value, $record->getValue());

        // 测试 recordId 属性
        $recordId = '12345';
        $record->setRecordId($recordId);
        $this->assertEquals($recordId, $record->getRecordId());

        // 测试 type 属性
        $type = DnsRecordType::A;
        $record->setType($type);
        $this->assertSame($type, $record->getType());

        // 测试 ttl 属性
        $ttl = 600;
        $record->setTtl($ttl);
        $this->assertEquals($ttl, $record->getTtl());

        // 测试 context 属性
        $context = ['key' => 'value'];
        $record->setContext($context);
        $this->assertEquals($context, $record->getContext());

        // 测试 valid 属性
        $valid = true;
        $record->setValid($valid);
        $this->assertEquals($valid, $record->isValid());

        // 测试 createdBy 属性
        $createdBy = 'admin';
        $record->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $record->getCreatedBy());

        // 测试 updatedBy 属性
        $updatedBy = 'admin2';
        $record->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $record->getUpdatedBy());

        // 测试 createTime 属性
        $createTime = new \DateTimeImmutable();
        $record->setCreateTime($createTime);
        $this->assertSame($createTime, $record->getCreateTime());

        // 测试 updateTime 属性
        $updateTime = new \DateTimeImmutable();
        $record->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $record->getUpdateTime());
    }

    public function testIdIsInitiallyNull(): void
    {
        $record = new DnsRecord();
        $this->assertNull($record->getId());
    }

    public function testEnumType(): void
    {
        $record = new DnsRecord();

        // 测试每种记录类型
        $types = [
            DnsRecordType::A,
            DnsRecordType::MX,
            DnsRecordType::TXT,
            DnsRecordType::CNAME,
            DnsRecordType::NS,
            DnsRecordType::URI,
        ];

        foreach ($types as $type) {
            $record->setType($type);
            $this->assertSame($type, $record->getType());
            $this->assertNotEmpty($type->getLabel());
        }
    }
}
