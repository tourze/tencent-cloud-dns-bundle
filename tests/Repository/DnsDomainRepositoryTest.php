<?php

namespace TencentCloudDnsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use TencentCloudDnsBundle\Repository\DnsDomainRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DnsDomainRepository::class)]
#[RunTestsInSeparateProcesses]
final class DnsDomainRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository tests don't need special setup
    }

    protected function createNewEntity(): object
    {
        // 创建一个虚拟的 Account 对象，不通过 Doctrine 管理
        // 这样可以满足 NOT NULL 约束，但不会触发级联持久化问题
        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());
        $account->setValid(true);

        // 直接创建账户实体，不使用反射API
        // 通过设置必要属性来满足NOT NULL约束

        $entity = new DnsDomain();
        $entity->setName('test-domain-' . uniqid() . '.com');
        $entity->setAccount($account);
        $entity->setValid(true);

        return $entity;
    }

    protected function getRepository(): DnsDomainRepository
    {
        $repository = self::getContainer()->get(DnsDomainRepository::class);
        $this->assertInstanceOf(DnsDomainRepository::class, $repository);

        return $repository;
    }

    private function getDnsDomainRepository(): DnsDomainRepository
    {
        return $this->getRepository();
    }

    public function testRepositoryInstance(): void
    {
        $repository = $this->getDnsDomainRepository();
        $this->assertInstanceOf(DnsDomainRepository::class, $repository);
    }

    public function testFindAll(): void
    {
        // 清空现有数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        // 测试空数据库的情况
        $repository = $this->getDnsDomainRepository();
        $domains = $repository->findAll();
        $this->assertIsArray($domains);
        $this->assertEmpty($domains);

        // 创建账户和域名
        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('example.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // 测试查找所有域名
        $domains = $repository->findAll();
        $this->assertCount(1, $domains);
        $this->assertInstanceOf(DnsDomain::class, $domains[0]);
        $this->assertEquals('example.com', $domains[0]->getName());
    }

    public function testSave(): void
    {
        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $domain = new DnsDomain();
        $domain->setName('save-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $repository->save($domain);

        $savedDomain = $repository->find($domain->getId());
        $this->assertNotNull($savedDomain);
        $this->assertEquals('save-test.com', $savedDomain->getName());
        $this->assertEquals($account->getId(), $savedDomain->getAccount()?->getId());
    }

    public function testSaveWithoutFlush(): void
    {
        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $domain = new DnsDomain();
        $domain->setName('no-flush-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $repository->save($domain, false);

        self::getEntityManager()->flush();

        $savedDomain = $repository->find($domain->getId());
        $this->assertNotNull($savedDomain);
        $this->assertEquals('no-flush-test.com', $savedDomain->getName());
    }

    public function testRemove(): void
    {
        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('remove-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();
        $domainId = $domain->getId();

        $this->assertNotNull($repository->find($domainId));

        $repository->remove($domain);

        $this->assertNull($repository->find($domainId));
    }

    public function testFind(): void
    {
        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('find-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $found = $repository->find($domain->getId());
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertEquals('find-test.com', $found->getName());
        $this->assertEquals($account->getId(), $found->getAccount()?->getId());
    }

    public function testCount(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $initialCount = $repository->count([]);
        $this->assertEquals(0, $initialCount);

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('count-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $count = $repository->count([]);
        $this->assertEquals(1, $count);

        // 测试条件计数
        $countValid = $repository->count(['valid' => true]);
        $this->assertEquals(1, $countValid);

        $countInvalid = $repository->count(['valid' => false]);
        $this->assertEquals(0, $countInvalid);
    }

    public function testFindBy(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain1 = new DnsDomain();
        $domain1->setName('findby-test1.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);

        $domain2 = new DnsDomain();
        $domain2->setName('findby-test2.com');
        $domain2->setAccount($account);
        $domain2->setValid(false);

        self::getEntityManager()->persist($domain1);
        self::getEntityManager()->persist($domain2);
        self::getEntityManager()->flush();

        // 测试无条件查找
        $allDomains = $repository->findBy([]);
        $this->assertIsArray($allDomains);
        $this->assertCount(2, $allDomains);

        // 测试条件查找
        $validDomains = $repository->findBy(['valid' => true]);
        $this->assertIsArray($validDomains);
        $this->assertCount(1, $validDomains);
        $this->assertEquals('findby-test1.com', $validDomains[0]->getName());

        // 测试不匹配条件
        $nonExistentDomains = $repository->findBy(['name' => 'non-existent.com']);
        $this->assertIsArray($nonExistentDomains);
        $this->assertEmpty($nonExistentDomains);
    }

    public function testFindByWithLimitAndOffset(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        for ($i = 1; $i <= 5; ++$i) {
            $domain = new DnsDomain();
            $domain->setName("limit-test{$i}.com");
            $domain->setAccount($account);
            $domain->setValid(true);
            self::getEntityManager()->persist($domain);
        }
        self::getEntityManager()->flush();

        // 测试分页
        $domains = $repository->findBy(['valid' => true], ['name' => 'ASC'], 2, 1);
        $this->assertIsArray($domains);
        $this->assertLessThanOrEqual(2, count($domains));
    }

    public function testFindOneBy(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findoneby-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // 测试匹配条件
        $found = $repository->findOneBy(['name' => 'findoneby-test.com']);
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertEquals('findoneby-test.com', $found->getName());

        // 测试不匹配条件
        $notFound = $repository->findOneBy(['name' => 'non-existent.com']);
        $this->assertNull($notFound);
    }

    public function testFindOneByWithOrderBy(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain1 = new DnsDomain();
        $domain1->setName('z-orderby-test.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);

        $domain2 = new DnsDomain();
        $domain2->setName('a-orderby-test.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);

        self::getEntityManager()->persist($domain1);
        self::getEntityManager()->persist($domain2);
        self::getEntityManager()->flush();

        // 测试排序返回第一个结果
        $found = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertEquals('a-orderby-test.com', $found->getName());
    }

    public function testFindByAccountAssociation(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account1 = new Account();
        $account1->setName('账户1');
        $account1->setSecretId('account1-secret-id');
        $account1->setSecretKey('account1-secret-key');
        $account1->setValid(true);
        self::getEntityManager()->persist($account1);

        $account2 = new Account();
        $account2->setName('账户2');
        $account2->setSecretId('account2-secret-id');
        $account2->setSecretKey('account2-secret-key');
        $account2->setValid(true);
        self::getEntityManager()->persist($account2);

        $domain1 = new DnsDomain();
        $domain1->setName('account1-domain.com');
        $domain1->setAccount($account1);
        $domain1->setValid(true);

        $domain2 = new DnsDomain();
        $domain2->setName('account2-domain.com');
        $domain2->setAccount($account2);
        $domain2->setValid(true);

        self::getEntityManager()->persist($domain1);
        self::getEntityManager()->persist($domain2);
        self::getEntityManager()->flush();

        // 测试通过关联实体查询
        $account1Domains = $repository->findBy(['account' => $account1]);
        $this->assertIsArray($account1Domains);
        $this->assertCount(1, $account1Domains);
        $this->assertEquals('account1-domain.com', $account1Domains[0]->getName());

        $account2Domains = $repository->findBy(['account' => $account2]);
        $this->assertIsArray($account2Domains);
        $this->assertCount(1, $account2Domains);
        $this->assertEquals('account2-domain.com', $account2Domains[0]->getName());
    }

    public function testFindByNullableFields(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('nullable-test.com');
        $domain->setAccount($account);
        $domain->setValid(null);
        $domain->setContext(null);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // 测试查询空值字段
        $nullValidDomains = $repository->findBy(['valid' => null]);
        $this->assertIsArray($nullValidDomains);
        $this->assertGreaterThanOrEqual(1, count($nullValidDomains));

        foreach ($nullValidDomains as $nullValidDomain) {
            $this->assertInstanceOf(DnsDomain::class, $nullValidDomain);
            $this->assertNull($nullValidDomain->isValid());
        }

        // 测试查询 context 为空的记录
        $nullContextDomains = $repository->findBy(['context' => null]);
        $this->assertIsArray($nullContextDomains);
        $this->assertGreaterThanOrEqual(1, count($nullContextDomains));

        foreach ($nullContextDomains as $nullContextDomain) {
            $this->assertInstanceOf(DnsDomain::class, $nullContextDomain);
            $this->assertNull($nullContextDomain->getContext());
        }
    }

    public function testCountNullableFields(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('count-nullable-test.com');
        $domain->setAccount($account);
        $domain->setValid(null);
        $domain->setContext(null);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // 测试计数空值字段
        $nullValidCount = $repository->count(['valid' => null]);
        $this->assertIsInt($nullValidCount);
        $this->assertGreaterThanOrEqual(1, $nullValidCount);

        $nullContextCount = $repository->count(['context' => null]);
        $this->assertIsInt($nullContextCount);
        $this->assertGreaterThanOrEqual(1, $nullContextCount);
    }

    public function testFindOneByNullableFields(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findoneby-nullable-test.com');
        $domain->setAccount($account);
        $domain->setValid(null);
        $domain->setContext(null);

        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // 测试通过空值字段查询单个记录
        $foundByValid = $repository->findOneBy(['valid' => null, 'name' => 'findoneby-nullable-test.com']);
        $this->assertInstanceOf(DnsDomain::class, $foundByValid);
        $this->assertNull($foundByValid->isValid());
        $this->assertEquals('findoneby-nullable-test.com', $foundByValid->getName());

        $foundByContext = $repository->findOneBy(['context' => null, 'name' => 'findoneby-nullable-test.com']);
        $this->assertInstanceOf(DnsDomain::class, $foundByContext);
        $this->assertNull($foundByContext->getContext());
        $this->assertEquals('findoneby-nullable-test.com', $foundByContext->getName());
    }

    public function testFindOneByOrderBySorting(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain1 = new DnsDomain();
        $domain1->setName('z-sorting-test.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);
        self::getEntityManager()->persist($domain1);

        $domain2 = new DnsDomain();
        $domain2->setName('a-sorting-test.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);
        self::getEntityManager()->persist($domain2);

        self::getEntityManager()->flush();

        // Test ascending order
        $foundAsc = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(DnsDomain::class, $foundAsc);

        // Test descending order
        $foundDesc = $repository->findOneBy(['valid' => true], ['name' => 'DESC']);
        $this->assertInstanceOf(DnsDomain::class, $foundDesc);

        // Verify sorting effect
        $this->assertNotEquals($foundAsc->getName(), $foundDesc->getName());
    }

    public function testCountByAccountAssociation(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account1 = new Account();
        $account1->setName('账户1');
        $account1->setSecretId('account1-secret-id');
        $account1->setSecretKey('account1-secret-key');
        $account1->setValid(true);
        self::getEntityManager()->persist($account1);

        $account2 = new Account();
        $account2->setName('账户2');
        $account2->setSecretId('account2-secret-id');
        $account2->setSecretKey('account2-secret-key');
        $account2->setValid(true);
        self::getEntityManager()->persist($account2);

        $domain1 = new DnsDomain();
        $domain1->setName('account1-count-domain.com');
        $domain1->setAccount($account1);
        $domain1->setValid(true);
        self::getEntityManager()->persist($domain1);

        $domain2 = new DnsDomain();
        $domain2->setName('account2-count-domain.com');
        $domain2->setAccount($account2);
        $domain2->setValid(true);
        self::getEntityManager()->persist($domain2);

        self::getEntityManager()->flush();

        // Test count by account association
        $account1Count = $repository->count(['account' => $account1]);
        $this->assertEquals(1, $account1Count);

        $account2Count = $repository->count(['account' => $account2]);
        $this->assertEquals(1, $account2Count);
    }

    public function testFindOneByAccountAssociation(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('关联账户');
        $account->setSecretId('assoc-secret-id');
        $account->setSecretKey('assoc-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('association-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // Test findOneBy with account association
        $found = $repository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertEquals('association-test.com', $found->getName());
        $this->assertEquals($account->getId(), $found->getAccount()?->getId());
    }

    public function testCountValidIsNull(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('count-valid-null.com');
        $domain->setAccount($account);
        $domain->setValid(null);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $count = $repository->count(['valid' => null]);
        $this->assertIsInt($count);
        $this->assertEquals(1, $count);
    }

    public function testCountContextIsNull(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('count-context-null.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        $domain->setContext(null);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $count = $repository->count(['context' => null]);
        $this->assertIsInt($count);
        $this->assertEquals(1, $count);
    }

    public function testFindByValidIsNull(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findby-valid-null.com');
        $domain->setAccount($account);
        $domain->setValid(null);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $results = $repository->findBy(['valid' => null]);
        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(DnsDomain::class, $results[0]);
        $this->assertNull($results[0]->isValid());
    }

    public function testFindByContextIsNull(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findby-context-null.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        $domain->setContext(null);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $results = $repository->findBy(['context' => null]);
        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(DnsDomain::class, $results[0]);
        $this->assertNull($results[0]->getContext());
    }

    public function testFindOneByOrderByLogic(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain1 = new DnsDomain();
        $domain1->setName('z-orderby-logic.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);
        self::getEntityManager()->persist($domain1);

        $domain2 = new DnsDomain();
        $domain2->setName('a-orderby-logic.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);
        self::getEntityManager()->persist($domain2);

        self::getEntityManager()->flush();

        // Test that findOneBy respects orderBy parameter
        $foundAsc = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(DnsDomain::class, $foundAsc);
        $this->assertEquals('a-orderby-logic.com', $foundAsc->getName());

        $foundDesc = $repository->findOneBy(['valid' => true], ['name' => 'DESC']);
        $this->assertInstanceOf(DnsDomain::class, $foundDesc);
        $this->assertEquals('z-orderby-logic.com', $foundDesc->getName());
    }

    public function testFindOneByOrderByLogicForSorting(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $prefix = 'findonebyorder_' . uniqid();

        $domain1 = new DnsDomain();
        $domain1->setName($prefix . '_domain_a.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);
        self::getEntityManager()->persist($domain1);

        $domain2 = new DnsDomain();
        $domain2->setName($prefix . '_domain_b.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);
        self::getEntityManager()->persist($domain2);

        self::getEntityManager()->flush();

        // Test ASC ordering
        $foundAsc = $repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(DnsDomain::class, $foundAsc);

        // Test DESC ordering
        $foundDesc = $repository->findOneBy(['valid' => true], ['name' => 'DESC']);
        $this->assertInstanceOf(DnsDomain::class, $foundDesc);

        // Verify ordering effect
        $this->assertNotEquals($foundAsc->getName(), $foundDesc->getName());
    }

    public function testFindOneByValidIsNull(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findoneby-valid-null.com');
        $domain->setAccount($account);
        $domain->setValid(null);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $found = $repository->findOneBy(['valid' => null, 'name' => 'findoneby-valid-null.com']);
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertNull($found->isValid());
        $this->assertEquals('findoneby-valid-null.com', $found->getName());
    }

    public function testFindOneByContextIsNull(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findoneby-context-null.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        $domain->setContext(null);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        $found = $repository->findOneBy(['context' => null, 'name' => 'findoneby-context-null.com']);
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertNull($found->getContext());
        $this->assertEquals('findoneby-context-null.com', $found->getName());
    }

    public function testFindByRecordsAssociation(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('records-association-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        self::getEntityManager()->persist($domain);

        $record = new DnsRecord();
        $record->setName('@');
        $record->setType(DnsRecordType::A);
        $record->setValue('192.168.1.1');
        $record->setDomain($domain);
        $record->setValid(true);
        $domain->addRecord($record);
        self::getEntityManager()->persist($record);

        self::getEntityManager()->flush();

        // 测试通过子记录对象查询域名（这里的逻辑是检查域名是否正确地与记录关联）
        $foundDomain = $repository->find($domain->getId());
        $this->assertInstanceOf(DnsDomain::class, $foundDomain);
        $this->assertCount(1, $foundDomain->getRecords());
        $firstRecord = $foundDomain->getRecords()->first();
        $this->assertInstanceOf(DnsRecord::class, $firstRecord);
        $this->assertEquals('@', $firstRecord->getName());
    }

    public function testCountRecordsAssociation(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain1 = new DnsDomain();
        $domain1->setName('count-records-1.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);
        self::getEntityManager()->persist($domain1);

        $domain2 = new DnsDomain();
        $domain2->setName('count-records-2.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);
        self::getEntityManager()->persist($domain2);

        $record1 = new DnsRecord();
        $record1->setName('@');
        $record1->setType(DnsRecordType::A);
        $record1->setValue('192.168.1.1');
        $record1->setDomain($domain1);
        $record1->setValid(true);
        $domain1->addRecord($record1);
        self::getEntityManager()->persist($record1);

        $record2 = new DnsRecord();
        $record2->setName('www');
        $record2->setType(DnsRecordType::CNAME);
        $record2->setValue('@');
        $record2->setDomain($domain1);
        $record2->setValid(true);
        $domain1->addRecord($record2);
        self::getEntityManager()->persist($record2);

        self::getEntityManager()->flush();

        // 测试计数域名（不是直接通过 records 关联字段查询，因为这是 OneToMany 关系）
        // 而是验证域名的记录集合是否正确加载
        $foundDomain1 = $repository->find($domain1->getId());
        $this->assertInstanceOf(DnsDomain::class, $foundDomain1);
        $this->assertCount(2, $foundDomain1->getRecords());

        $foundDomain2 = $repository->find($domain2->getId());
        $this->assertInstanceOf(DnsDomain::class, $foundDomain2);
        $this->assertCount(0, $foundDomain2->getRecords());
    }

    public function testFindOneByRecordsAssociation(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('findoneby-records.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        self::getEntityManager()->persist($domain);

        $record = new DnsRecord();
        $record->setName('api');
        $record->setType(DnsRecordType::A);
        $record->setValue('203.0.113.1');
        $record->setDomain($domain);
        $record->setValid(true);
        $domain->addRecord($record);
        self::getEntityManager()->persist($record);

        self::getEntityManager()->flush();

        // 测试找到具有记录的域名
        $foundDomain = $repository->findOneBy(['name' => 'findoneby-records.com']);
        $this->assertInstanceOf(DnsDomain::class, $foundDomain);
        $this->assertCount(1, $foundDomain->getRecords());
        $firstRecord = $foundDomain->getRecords()->first();
        $this->assertInstanceOf(DnsRecord::class, $firstRecord);
        $this->assertEquals('api', $firstRecord->getName());
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account1 = new Account();
        $account1->setName('账户1');
        $account1->setSecretId('account1-secret-id');
        $account1->setSecretKey('account1-secret-key');
        $account1->setValid(true);
        self::getEntityManager()->persist($account1);

        $account2 = new Account();
        $account2->setName('账户2');
        $account2->setSecretId('account2-secret-id');
        $account2->setSecretKey('account2-secret-key');
        $account2->setValid(true);
        self::getEntityManager()->persist($account2);

        for ($i = 1; $i <= 3; ++$i) {
            $domain = new DnsDomain();
            $domain->setName("account1-domain-{$i}.com");
            $domain->setAccount($account1);
            $domain->setValid(true);
            self::getEntityManager()->persist($domain);
        }

        for ($i = 1; $i <= 2; ++$i) {
            $domain = new DnsDomain();
            $domain->setName("account2-domain-{$i}.com");
            $domain->setAccount($account2);
            $domain->setValid(true);
            self::getEntityManager()->persist($domain);
        }

        self::getEntityManager()->flush();

        // Test count by account association
        $account1Count = $repository->count(['account' => $account1]);
        $this->assertEquals(3, $account1Count);

        $account2Count = $repository->count(['account' => $account2]);
        $this->assertEquals(2, $account2Count);

        // Test count with combined criteria
        $validAccount1Count = $repository->count(['account' => $account1, 'valid' => true]);
        $this->assertEquals(3, $validAccount1Count);

        $invalidAccount1Count = $repository->count(['account' => $account1, 'valid' => false]);
        $this->assertEquals(0, $invalidAccount1Count);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsDomainRepository();

        $account = new Account();
        $account->setName('关联账户');
        $account->setSecretId('assoc-secret-id');
        $account->setSecretKey('assoc-secret-key');
        $account->setValid(true);
        self::getEntityManager()->persist($account);

        $domain = new DnsDomain();
        $domain->setName('association-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->flush();

        // Test findOneBy with account association
        $found = $repository->findOneBy(['account' => $account]);
        $this->assertInstanceOf(DnsDomain::class, $found);
        $this->assertEquals('association-test.com', $found->getName());
        $this->assertEquals($account->getId(), $found->getAccount()?->getId());
    }
}
