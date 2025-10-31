<?php

namespace TencentCloudDnsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudDnsBundle\Entity\Account;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Account();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试账号'];
        yield 'secretId' => ['secretId', 'test-secret-id'];
        yield 'secretKey' => ['secretKey', 'test-secret-key'];
        yield 'valid' => ['valid', true];
        yield 'createdBy' => ['createdBy', 'admin'];
        yield 'updatedBy' => ['updatedBy', 'admin2'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    public function testGetterAndSettersManual(): void
    {
        $account = new Account();

        // 测试 name 属性
        $name = '测试账号';
        $account->setName($name);
        $this->assertEquals($name, $account->getName());

        // 测试 secretId 属性
        $secretId = 'test-secret-id';
        $account->setSecretId($secretId);
        $this->assertEquals($secretId, $account->getSecretId());

        // 测试 secretKey 属性
        $secretKey = 'test-secret-key';
        $account->setSecretKey($secretKey);
        $this->assertEquals($secretKey, $account->getSecretKey());

        // 测试 valid 属性
        $valid = true;
        $account->setValid($valid);
        $this->assertEquals($valid, $account->isValid());

        // 测试 createdBy 属性
        $createdBy = 'admin';
        $account->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $account->getCreatedBy());

        // 测试 updatedBy 属性
        $updatedBy = 'admin2';
        $account->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $account->getUpdatedBy());

        // 测试 createTime 属性
        $createTime = new \DateTimeImmutable();
        $account->setCreateTime($createTime);
        $this->assertSame($createTime, $account->getCreateTime());

        // 测试 updateTime 属性
        $updateTime = new \DateTimeImmutable();
        $account->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $account->getUpdateTime());
    }

    public function testIdIsInitiallyNull(): void
    {
        $account = new Account();
        $this->assertNull($account->getId());
    }
}
