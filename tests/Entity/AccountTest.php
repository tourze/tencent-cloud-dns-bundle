<?php

namespace TencentCloudDnsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Entity\Account;

class AccountTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account();
    }

    public function testGetterAndSetters(): void
    {
        // 测试 name 属性
        $name = '测试账号';
        $this->account->setName($name);
        $this->assertEquals($name, $this->account->getName());

        // 测试 secretId 属性
        $secretId = 'test-secret-id';
        $this->account->setSecretId($secretId);
        $this->assertEquals($secretId, $this->account->getSecretId());

        // 测试 secretKey 属性
        $secretKey = 'test-secret-key';
        $this->account->setSecretKey($secretKey);
        $this->assertEquals($secretKey, $this->account->getSecretKey());

        // 测试 valid 属性
        $valid = true;
        $this->account->setValid($valid);
        $this->assertEquals($valid, $this->account->isValid());

        // 测试 createdBy 属性
        $createdBy = 'admin';
        $this->account->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->account->getCreatedBy());

        // 测试 updatedBy 属性
        $updatedBy = 'admin2';
        $this->account->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->account->getUpdatedBy());

        // 测试 createTime 属性
        $createTime = new \DateTime();
        $this->account->setCreateTime($createTime);
        $this->assertSame($createTime, $this->account->getCreateTime());

        // 测试 updateTime 属性
        $updateTime = new \DateTime();
        $this->account->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->account->getUpdateTime());
    }

    public function testIdIsInitiallyZero(): void
    {
        $this->assertEquals(0, $this->account->getId());
    }
}
