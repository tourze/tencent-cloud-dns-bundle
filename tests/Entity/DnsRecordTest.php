<?php

namespace TencentCloudDnsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;

class DnsRecordTest extends TestCase
{
    private DnsRecord $record;
    private DnsDomain $domain;

    protected function setUp(): void
    {
        $this->record = new DnsRecord();
        $this->domain = new DnsDomain();
        $this->domain->setName('example.com');
    }

    public function testGetterAndSetters(): void
    {
        // 测试 domain 属性
        $this->record->setDomain($this->domain);
        $this->assertSame($this->domain, $this->record->getDomain());

        // 测试 name 属性
        $name = 'test';
        $this->record->setName($name);
        $this->assertEquals($name, $this->record->getName());

        // 测试 value 属性
        $value = '192.168.1.1';
        $this->record->setValue($value);
        $this->assertEquals($value, $this->record->getValue());

        // 测试 recordId 属性
        $recordId = '12345';
        $this->record->setRecordId($recordId);
        $this->assertEquals($recordId, $this->record->getRecordId());

        // 测试 type 属性
        $type = DnsRecordType::A;
        $this->record->setType($type);
        $this->assertSame($type, $this->record->getType());

        // 测试 ttl 属性
        $ttl = 600;
        $this->record->setTtl($ttl);
        $this->assertEquals($ttl, $this->record->getTtl());

        // 测试 context 属性
        $context = ['key' => 'value'];
        $this->record->setContext($context);
        $this->assertEquals($context, $this->record->getContext());

        // 测试 valid 属性
        $valid = true;
        $this->record->setValid($valid);
        $this->assertEquals($valid, $this->record->isValid());

        // 测试 createdBy 属性
        $createdBy = 'admin';
        $this->record->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->record->getCreatedBy());

        // 测试 updatedBy 属性
        $updatedBy = 'admin2';
        $this->record->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->record->getUpdatedBy());

        // 测试 createTime 属性
        $createTime = new \DateTime();
        $this->record->setCreateTime($createTime);
        $this->assertSame($createTime, $this->record->getCreateTime());

        // 测试 updateTime 属性
        $updateTime = new \DateTime();
        $this->record->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->record->getUpdateTime());
    }

    public function testIdIsInitiallyZero(): void
    {
        $this->assertEquals(0, $this->record->getId());
    }

    public function testEnumType(): void
    {
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
            $this->record->setType($type);
            $this->assertSame($type, $this->record->getType());
            $this->assertIsString($type->value);
            $this->assertNotEmpty($type->getLabel());
        }
    }
}
