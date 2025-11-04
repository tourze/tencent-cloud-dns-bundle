<?php

namespace TencentCloudDnsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Repository\AccountRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<Account>
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository tests don't need special setup
    }

    protected function createNewEntity(): object
    {
        $entity = new Account();
        $entity->setName('Test Account ' . uniqid());
        $entity->setSecretId('test_secret_id_' . uniqid());
        $entity->setSecretKey('test_secret_key_' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository
    {
        $repository = self::getContainer()->get(AccountRepository::class);
        $this->assertInstanceOf(AccountRepository::class, $repository);

        return $repository;
    }

    private function getAccountRepository(): AccountRepository
    {
        /** @var AccountRepository $repository */
        $repository = $this->getRepository();

        return $repository;
    }

    public function testRepositoryInstance(): void
    {
        $repository = $this->getAccountRepository();
        $this->assertInstanceOf(AccountRepository::class, $repository);
    }

    public function testFindAll(): void
    {
        // 清空现有数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        // 测试空数据库的情况
        $repository = $this->getAccountRepository();
        $accounts = $repository->findAll();
        $this->assertEmpty($accounts);

        // 创建一个账户
        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        // 测试查找所有账户
        $accounts = $repository->findAll();
        $this->assertCount(1, $accounts);
        $this->assertInstanceOf(Account::class, $accounts[0]);
        $this->assertEquals('测试账户', $accounts[0]->getName());
    }

    public function testSave(): void
    {
        $repository = $this->getAccountRepository();

        $account = new Account();
        $account->setName('保存测试账户');
        $account->setSecretId('save-test-id');
        $account->setSecretKey('save-test-key');
        $account->setValid(true);

        // 使用 save 方法保存
        $repository->save($account);

        // 验证保存成功
        $savedAccount = $repository->find($account->getId());
        $this->assertNotNull($savedAccount);
        $this->assertEquals('保存测试账户', $savedAccount->getName());
    }

    public function testSaveWithoutFlush(): void
    {
        $repository = $this->getAccountRepository();

        $account = new Account();
        $account->setName('不刷新测试账户');
        $account->setSecretId('no-flush-id');
        $account->setSecretKey('no-flush-key');
        $account->setValid(true);

        // 使用 save 方法但不刷新
        $repository->save($account, false);

        // 手动刷新
        self::getEntityManager()->flush();

        // 验证保存成功
        $savedAccount = $repository->find($account->getId());
        $this->assertNotNull($savedAccount);
        $this->assertEquals('不刷新测试账户', $savedAccount->getName());
    }

    public function testRemove(): void
    {
        $repository = $this->getAccountRepository();

        // 先创建一个账户
        $account = new Account();
        $account->setName('删除测试账户');
        $account->setSecretId('remove-test-id');
        $account->setSecretKey('remove-test-key');
        $account->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();
        $accountId = $account->getId();

        // 确认账户存在
        $this->assertNotNull($repository->find($accountId));

        // 使用 remove 方法删除
        $repository->remove($account);

        // 验证删除成功
        $this->assertNull($repository->find($accountId));
    }

    // ========== find 系列方法测试 ==========

    // ========== findBy 系列方法测试 ==========

    // ========== findOneBy 系列方法测试 ==========

    public function testFindOneByWithOrderByShouldReturnFirstMatch(): void
    {
        $repository = $this->getAccountRepository();

        // 清空现有数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        // 创建多个有效账户
        $account1 = new Account();
        $account1->setName('Z最后');
        $account1->setSecretId('z-last-id');
        $account1->setSecretKey('z-last-key');
        $account1->setValid(true);

        $account2 = new Account();
        $account2->setName('A最前');
        $account2->setSecretId('a-first-id');
        $account2->setSecretKey('a-first-key');
        $account2->setValid(true);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        // 测试排序后的第一个结果
        $firstAccount = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertNotNull($firstAccount);
        $this->assertEquals('A最前', $firstAccount->getName());
    }
}
