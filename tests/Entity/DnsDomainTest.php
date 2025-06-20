<?php

namespace TencentCloudDnsBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;

class DnsDomainTest extends TestCase
{
    private DnsDomain $domain;
    private Account $account;

    protected function setUp(): void
    {
        $this->domain = new DnsDomain();
        $this->account = new Account();
        $this->account->setName('测试账号');
        $this->account->setSecretId('test-secret-id');
        $this->account->setSecretKey('test-secret-key');
    }

    public function testGetterAndSetters(): void
    {
        // 测试 name 属性
        $name = 'example.com';
        $this->domain->setName($name);
        $this->assertEquals($name, $this->domain->getName());

        // 测试 account 属性
        $this->domain->setAccount($this->account);
        $this->assertSame($this->account, $this->domain->getAccount());

        // 测试 valid 属性
        $valid = true;
        $this->domain->setValid($valid);
        $this->assertEquals($valid, $this->domain->isValid());

        // 测试 context 属性
        $context = ['key' => 'value'];
        $this->domain->setContext($context);
        $this->assertEquals($context, $this->domain->getContext());

        // 测试 createdBy 属性
        $createdBy = 'admin';
        $this->domain->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->domain->getCreatedBy());

        // 测试 updatedBy 属性
        $updatedBy = 'admin2';
        $this->domain->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->domain->getUpdatedBy());

        // 测试 createTime 属性
        $createTime = new \DateTimeImmutable();
        $this->domain->setCreateTime($createTime);
        $this->assertSame($createTime, $this->domain->getCreateTime());

        // 测试 updateTime 属性
        $updateTime = new \DateTimeImmutable();
        $this->domain->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->domain->getUpdateTime());
    }

    public function testIdIsInitiallyZero(): void
    {
        $this->assertEquals(0, $this->domain->getId());
    }

    public function testRecordCollectionInitialization(): void
    {
        $records = $this->domain->getRecords();
        $this->assertInstanceOf(Collection::class, $records);
        $this->assertCount(0, $records);
    }

    public function testAddAndRemoveRecord(): void
    {
        $record = new DnsRecord();

        // 测试添加记录
        $this->domain->addRecord($record);
        $this->assertCount(1, $this->domain->getRecords());
        $this->assertSame($this->domain, $record->getDomain());

        // 再次添加相同记录不应改变集合大小
        $this->domain->addRecord($record);
        $this->assertCount(1, $this->domain->getRecords());

        // 测试移除记录
        $this->domain->removeRecord($record);
        $this->assertCount(0, $this->domain->getRecords());
        $this->assertNull($record->getDomain());
    }
}
