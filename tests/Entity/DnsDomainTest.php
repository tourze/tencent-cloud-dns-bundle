<?php

namespace TencentCloudDnsBundle\Tests\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DnsDomain::class)]
final class DnsDomainTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new DnsDomain();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        yield 'name' => ['name', 'example.com'];
        yield 'account' => ['account', $account];
        yield 'valid' => ['valid', true];
        yield 'context' => ['context', ['key' => 'value']];
        yield 'createdBy' => ['createdBy', 'admin'];
        yield 'updatedBy' => ['updatedBy', 'admin2'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    public function testGetterAndSettersManual(): void
    {
        $domain = new DnsDomain();
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        // 测试 name 属性
        $name = 'example.com';
        $domain->setName($name);
        $this->assertEquals($name, $domain->getName());

        // 测试 account 属性
        $domain->setAccount($account);
        $this->assertSame($account, $domain->getAccount());

        // 测试 valid 属性
        $valid = true;
        $domain->setValid($valid);
        $this->assertEquals($valid, $domain->isValid());

        // 测试 context 属性
        $context = ['key' => 'value'];
        $domain->setContext($context);
        $this->assertEquals($context, $domain->getContext());

        // 测试 createdBy 属性
        $createdBy = 'admin';
        $domain->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $domain->getCreatedBy());

        // 测试 updatedBy 属性
        $updatedBy = 'admin2';
        $domain->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $domain->getUpdatedBy());

        // 测试 createTime 属性
        $createTime = new \DateTimeImmutable();
        $domain->setCreateTime($createTime);
        $this->assertSame($createTime, $domain->getCreateTime());

        // 测试 updateTime 属性
        $updateTime = new \DateTimeImmutable();
        $domain->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $domain->getUpdateTime());
    }

    public function testIdIsInitiallyNull(): void
    {
        $domain = new DnsDomain();
        $this->assertNull($domain->getId());
    }

    public function testRecordCollectionInitialization(): void
    {
        $domain = new DnsDomain();
        $records = $domain->getRecords();
        $this->assertInstanceOf(Collection::class, $records);
        $this->assertCount(0, $records);
    }

    public function testAddAndRemoveRecord(): void
    {
        $domain = new DnsDomain();
        $record = new DnsRecord();

        // 测试添加记录
        $domain->addRecord($record);
        $this->assertCount(1, $domain->getRecords());
        $this->assertSame($domain, $record->getDomain());

        // 再次添加相同记录不应改变集合大小
        $domain->addRecord($record);
        $this->assertCount(1, $domain->getRecords());

        // 测试移除记录
        $domain->removeRecord($record);
        $this->assertCount(0, $domain->getRecords());
        $this->assertNull($record->getDomain());
    }
}
