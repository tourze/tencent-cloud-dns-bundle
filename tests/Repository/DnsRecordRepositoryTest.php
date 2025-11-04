<?php

namespace TencentCloudDnsBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use TencentCloudDnsBundle\Repository\DnsRecordRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @template-extends AbstractRepositoryTestCase<DnsRecord>
 * @internal
 */
#[CoversClass(DnsRecordRepository::class)]
#[RunTestsInSeparateProcesses]
final class DnsRecordRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository tests don't need special setup
    }

    protected function createNewEntity(): object
    {
        // 创建虚拟的关联对象，不通过 Doctrine 管理
        // 这样可以满足 NOT NULL 约束，但不会触发级联持久化问题
        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());
        $account->setValid(true);

        // 直接创建账户实体，不使用反射API
        // 通过设置必要属性来满足NOT NULL约束

        $domain = new DnsDomain();
        $domain->setName('test-domain-' . uniqid() . '.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        // 直接创建域名实体，不使用反射API

        $entity = new DnsRecord();
        $entity->setName('test-record');
        $entity->setType(DnsRecordType::A);
        $entity->setValue('192.168.1.1');
        $entity->setDomain($domain);
        $entity->setValid(true);

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository
    {
        $repository = self::getContainer()->get(DnsRecordRepository::class);
        $this->assertInstanceOf(DnsRecordRepository::class, $repository);

        return $repository;
    }

    private function getDnsRecordRepository(): DnsRecordRepository
    {
        /** @var DnsRecordRepository $repository */
        $repository = $this->getRepository();

        return $repository;
    }

    public function testRepositoryInstance(): void
    {
        $repository = $this->getDnsRecordRepository();
        $this->assertInstanceOf(DnsRecordRepository::class, $repository);
    }

    public function testFindAll(): void
    {
        // 清空现有数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        // 测试空数据库的情况
        $repository = $this->getDnsRecordRepository();
        $records = $repository->findAll();
        $this->assertEmpty($records);

        // 创建账户、域名和记录
        $account = new Account();
        $account->setName('测试账户');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('example.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('test');
        $record->setValue('192.168.1.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(600);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record);
        self::getEntityManager()->flush();

        // 测试查找所有记录
        $records = $repository->findAll();
        $this->assertCount(1, $records);
        $this->assertInstanceOf(DnsRecord::class, $records[0]);
        $this->assertEquals('test', $records[0]->getName());
        $this->assertEquals('192.168.1.1', $records[0]->getValue());
    }

    public function testSave(): void
    {
        $repository = $this->getDnsRecordRepository();

        $account = new Account();
        $account->setName('保存测试账户');
        $account->setSecretId('save-test-id');
        $account->setSecretKey('save-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('save-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('save-test');
        $record->setValue('10.0.0.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(300);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);

        $repository->save($record);

        $savedRecord = $repository->find($record->getId());
        $this->assertNotNull($savedRecord);
        $this->assertEquals('save-test', $savedRecord->getName());
        $this->assertEquals('10.0.0.1', $savedRecord->getValue());
    }

    public function testSaveWithoutFlush(): void
    {
        $repository = $this->getDnsRecordRepository();

        $account = new Account();
        $account->setName('不刷新测试账户');
        $account->setSecretId('no-flush-id');
        $account->setSecretKey('no-flush-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('no-flush-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('no-flush-test');
        $record->setValue('10.0.0.2');
        $record->setType(DnsRecordType::A);
        $record->setTtl(300);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);

        $repository->save($record, false);

        self::getEntityManager()->flush();

        $savedRecord = $repository->find($record->getId());
        $this->assertNotNull($savedRecord);
        $this->assertEquals('no-flush-test', $savedRecord->getName());
        $this->assertEquals('10.0.0.2', $savedRecord->getValue());
    }

    public function testRemove(): void
    {
        $repository = $this->getDnsRecordRepository();

        $account = new Account();
        $account->setName('删除测试账户');
        $account->setSecretId('remove-test-id');
        $account->setSecretKey('remove-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('remove-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('remove-test');
        $record->setValue('10.0.0.3');
        $record->setType(DnsRecordType::A);
        $record->setTtl(300);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record);
        self::getEntityManager()->flush();
        $recordId = $record->getId();

        $this->assertNotNull($repository->find($recordId));

        $repository->remove($record);

        $this->assertNull($repository->find($recordId));
    }

    public function testFind(): void
    {
        $repository = $this->getDnsRecordRepository();

        // 测试查找不存在的记录
        $notFound = $repository->find(999999);
        $this->assertNull($notFound);

        // 创建测试数据
        $account = new Account();
        $account->setName('查找测试账户');
        $account->setSecretId('find-test-id');
        $account->setSecretKey('find-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('find-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('find-test');
        $record->setValue('192.168.100.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(3600);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record);
        self::getEntityManager()->flush();

        // 测试查找存在的记录
        $found = $repository->find($record->getId());
        $this->assertNotNull($found);
        $this->assertInstanceOf(DnsRecord::class, $found);
        $this->assertEquals('find-test', $found->getName());
        $this->assertEquals('192.168.100.1', $found->getValue());
        $this->assertEquals(DnsRecordType::A, $found->getType());
        $this->assertEquals(3600, $found->getTtl());
        $this->assertTrue($found->isValid());
    }

    public function testFindBy(): void
    {
        // 清空数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsRecordRepository();

        // 创建测试数据
        $account = new Account();
        $account->setName('FindBy测试账户');
        $account->setSecretId('findby-test-id');
        $account->setSecretKey('findby-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('findby-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record1 = new DnsRecord();
        $record1->setDomain($domain);
        $record1->setName('www');
        $record1->setValue('192.168.1.1');
        $record1->setType(DnsRecordType::A);
        $record1->setTtl(300);
        $record1->setValid(true);

        $record2 = new DnsRecord();
        $record2->setDomain($domain);
        $record2->setName('mail');
        $record2->setValue('192.168.1.2');
        $record2->setType(DnsRecordType::A);
        $record2->setTtl(600);
        $record2->setValid(false);

        $record3 = new DnsRecord();
        $record3->setDomain($domain);
        $record3->setName('ftp');
        $record3->setValue('ftp.example.com');
        $record3->setType(DnsRecordType::CNAME);
        $record3->setTtl(300);
        $record3->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record1);
        self::getEntityManager()->persist($record2);
        self::getEntityManager()->persist($record3);
        self::getEntityManager()->flush();

        // 测试按类型查找
        $aRecords = $repository->findBy(['type' => DnsRecordType::A]);
        $this->assertCount(2, $aRecords);
        $this->assertEquals(DnsRecordType::A, $aRecords[0]->getType());
        $this->assertEquals(DnsRecordType::A, $aRecords[1]->getType());

        // 测试按有效性查找
        $validRecords = $repository->findBy(['valid' => true]);
        $this->assertCount(2, $validRecords);
        foreach ($validRecords as $record) {
            $this->assertTrue($record->isValid());
        }

        // 测试按域名查找
        $domainRecords = $repository->findBy(['domain' => $domain]);
        $this->assertCount(3, $domainRecords);

        // 测试按TTL查找
        $ttl300Records = $repository->findBy(['ttl' => 300]);
        $this->assertCount(2, $ttl300Records);

        // 测试组合条件查找
        $combinedRecords = $repository->findBy(['type' => DnsRecordType::A, 'valid' => true]);
        $this->assertCount(1, $combinedRecords);
        $this->assertEquals('www', $combinedRecords[0]->getName());

        // 测试排序
        $sortedRecords = $repository->findBy([], ['name' => 'ASC']);
        $this->assertCount(3, $sortedRecords);
        $this->assertEquals('ftp', $sortedRecords[0]->getName());
        $this->assertEquals('mail', $sortedRecords[1]->getName());
        $this->assertEquals('www', $sortedRecords[2]->getName());

        // 测试限制数量
        $limitedRecords = $repository->findBy([], ['name' => 'ASC'], 2);
        $this->assertCount(2, $limitedRecords);

        // 测试偏移量
        $offsetRecords = $repository->findBy([], ['name' => 'ASC'], 2, 1);
        $this->assertCount(2, $offsetRecords);
        $this->assertEquals('mail', $offsetRecords[0]->getName());
        $this->assertEquals('www', $offsetRecords[1]->getName());
    }

    public function testFindOneBy(): void
    {
        $repository = $this->getDnsRecordRepository();

        // 创建测试数据
        $account = new Account();
        $account->setName('FindOneBy测试账户');
        $account->setSecretId('findoneby-test-id');
        $account->setSecretKey('findoneby-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('findoneby-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('unique-record');
        $record->setValue('10.0.0.100');
        $record->setType(DnsRecordType::A);
        $record->setTtl(1800);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record);
        self::getEntityManager()->flush();

        // 测试查找存在的记录
        $found = $repository->findOneBy(['name' => 'unique-record']);
        $this->assertNotNull($found);
        $this->assertInstanceOf(DnsRecord::class, $found);
        $this->assertEquals('unique-record', $found->getName());
        $this->assertEquals('10.0.0.100', $found->getValue());

        // 测试查找不存在的记录
        $notFound = $repository->findOneBy(['name' => 'non-existent']);
        $this->assertNull($notFound);

        // 测试按多个条件查找
        $multiCondition = $repository->findOneBy([
            'name' => 'unique-record',
            'type' => DnsRecordType::A,
            'valid' => true,
        ]);
        $this->assertNotNull($multiCondition);
        $this->assertEquals('unique-record', $multiCondition->getName());

        // 测试排序
        $withOrder = $repository->findOneBy(['domain' => $domain], ['name' => 'DESC']);
        $this->assertNotNull($withOrder);
    }

    public function testFindByWithNullableFields(): void
    {
        // 清空数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsRecordRepository();

        // 创建测试数据
        $account = new Account();
        $account->setName('可空字段测试账户');
        $account->setSecretId('nullable-test-id');
        $account->setSecretKey('nullable-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('nullable-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        // 创建有TTL的记录
        $recordWithTtl = new DnsRecord();
        $recordWithTtl->setDomain($domain);
        $recordWithTtl->setName('with-ttl');
        $recordWithTtl->setValue('192.168.1.10');
        $recordWithTtl->setType(DnsRecordType::A);
        $recordWithTtl->setTtl(3600);
        $recordWithTtl->setRecordId('remote-ttl-123');
        $recordWithTtl->setValid(true);

        // 创建没有TTL的记录
        $recordWithoutTtl = new DnsRecord();
        $recordWithoutTtl->setDomain($domain);
        $recordWithoutTtl->setName('without-ttl');
        $recordWithoutTtl->setValue('192.168.1.11');
        $recordWithoutTtl->setType(DnsRecordType::A);
        $recordWithoutTtl->setTtl(null);
        $recordWithoutTtl->setRecordId('remote-no-ttl-456');
        $recordWithoutTtl->setValid(true);

        // 创建有recordId的记录
        $recordWithRecordId = new DnsRecord();
        $recordWithRecordId->setDomain($domain);
        $recordWithRecordId->setName('with-record-id');
        $recordWithRecordId->setValue('192.168.1.12');
        $recordWithRecordId->setType(DnsRecordType::A);
        $recordWithRecordId->setTtl(300);
        $recordWithRecordId->setRecordId('remote-123');
        $recordWithRecordId->setValid(true);

        // 创建没有recordId的记录
        $recordWithoutRecordId = new DnsRecord();
        $recordWithoutRecordId->setDomain($domain);
        $recordWithoutRecordId->setName('without-record-id');
        $recordWithoutRecordId->setValue('192.168.1.13');
        $recordWithoutRecordId->setType(DnsRecordType::A);
        $recordWithoutRecordId->setTtl(600);
        $recordWithoutRecordId->setRecordId(null);
        $recordWithoutRecordId->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($recordWithTtl);
        self::getEntityManager()->persist($recordWithoutTtl);
        self::getEntityManager()->persist($recordWithRecordId);
        self::getEntityManager()->persist($recordWithoutRecordId);
        self::getEntityManager()->flush();

        // 测试查找有TTL的记录
        $recordsWithTtl = $repository->findBy(['ttl' => 3600]);
        $this->assertCount(1, $recordsWithTtl);
        $this->assertEquals('with-ttl', $recordsWithTtl[0]->getName());

        // 测试查找有recordId的记录
        $recordsWithRecordId = $repository->findBy(['recordId' => 'remote-123']);
        $this->assertCount(1, $recordsWithRecordId);
        $this->assertEquals('with-record-id', $recordsWithRecordId[0]->getName());

        // 使用DQL测试NULL查询
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $recordsWithNullTtl */
        $recordsWithNullTtl = $qb
            ->where('r.ttl IS NULL')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(1, $recordsWithNullTtl);
        $this->assertEquals('without-ttl', $recordsWithNullTtl[0]->getName());

        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $recordsWithNullRecordId */
        $recordsWithNullRecordId = $qb
            ->where('r.recordId IS NULL')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(1, $recordsWithNullRecordId);
        $this->assertEquals('without-record-id', $recordsWithNullRecordId[0]->getName());

        // 测试IS NOT NULL查询
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $recordsWithNotNullTtl */
        $recordsWithNotNullTtl = $qb
            ->where('r.ttl IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(3, $recordsWithNotNullTtl); // recordWithTtl + recordWithRecordId + recordWithoutRecordId
    }

    public function testFindByDomainAssociation(): void
    {
        // 清空数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsRecordRepository();

        // 创建测试数据
        $account = new Account();
        $account->setName('关联测试账户');
        $account->setSecretId('association-test-id');
        $account->setSecretKey('association-test-key');
        $account->setValid(true);

        $domain1 = new DnsDomain();
        $domain1->setName('domain1.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);

        $domain2 = new DnsDomain();
        $domain2->setName('domain2.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);

        // Domain1的记录
        $record1 = new DnsRecord();
        $record1->setDomain($domain1);
        $record1->setName('www');
        $record1->setValue('192.168.1.1');
        $record1->setType(DnsRecordType::A);
        $record1->setValid(true);

        $record2 = new DnsRecord();
        $record2->setDomain($domain1);
        $record2->setName('mail');
        $record2->setValue('192.168.1.2');
        $record2->setType(DnsRecordType::A);
        $record2->setValid(true);

        // Domain2的记录
        $record3 = new DnsRecord();
        $record3->setDomain($domain2);
        $record3->setName('www');
        $record3->setValue('192.168.2.1');
        $record3->setType(DnsRecordType::A);
        $record3->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain1);
        self::getEntityManager()->persist($domain2);
        self::getEntityManager()->persist($record1);
        self::getEntityManager()->persist($record2);
        self::getEntityManager()->persist($record3);
        self::getEntityManager()->flush();

        // 测试按域名查找记录
        $domain1Records = $repository->findBy(['domain' => $domain1]);
        $this->assertCount(2, $domain1Records);
        foreach ($domain1Records as $record) {
            $this->assertNotNull($record->getDomain());
            $this->assertEquals($domain1->getId(), $record->getDomain()->getId());
        }

        $domain2Records = $repository->findBy(['domain' => $domain2]);
        $this->assertCount(1, $domain2Records);
        $this->assertNotNull($domain2Records[0]->getDomain());
        $this->assertEquals($domain2->getId(), $domain2Records[0]->getDomain()->getId());

        // 使用JOIN查询测试域名关联
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $recordsWithDomainInfo */
        $recordsWithDomainInfo = $qb
            ->select('r', 'd')
            ->join('r.domain', 'd')
            ->where('d.name = :domainName')
            ->setParameter('domainName', 'domain1.com')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(2, $recordsWithDomainInfo);

        // 测试通过域名名称查找记录
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $recordsByDomainName */
        $recordsByDomainName = $qb
            ->join('r.domain', 'd')
            ->where('d.name = :domainName')
            ->setParameter('domainName', 'domain2.com')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(1, $recordsByDomainName);
        $this->assertEquals('www', $recordsByDomainName[0]->getName());
        $this->assertEquals('192.168.2.1', $recordsByDomainName[0]->getValue());
    }

    public function testCountQueries(): void
    {
        // 清空数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsRecordRepository();

        // 测试空数据库计数
        $qb = $repository->createQueryBuilder('r');
        $emptyCount = $qb->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(0, $emptyCount);

        // 创建测试数据
        $account = new Account();
        $account->setName('计数测试账户');
        $account->setSecretId('count-test-id');
        $account->setSecretKey('count-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('count-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record1 = new DnsRecord();
        $record1->setDomain($domain);
        $record1->setName('www');
        $record1->setValue('192.168.1.1');
        $record1->setType(DnsRecordType::A);
        $record1->setValid(true);

        $record2 = new DnsRecord();
        $record2->setDomain($domain);
        $record2->setName('mail');
        $record2->setValue('192.168.1.2');
        $record2->setType(DnsRecordType::A);
        $record2->setValid(false);

        $record3 = new DnsRecord();
        $record3->setDomain($domain);
        $record3->setName('ftp');
        $record3->setValue('ftp.example.com');
        $record3->setType(DnsRecordType::CNAME);
        $record3->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record1);
        self::getEntityManager()->persist($record2);
        self::getEntityManager()->persist($record3);
        self::getEntityManager()->flush();

        // 测试总记录数
        $qb = $repository->createQueryBuilder('r');
        $totalCount = $qb->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(3, $totalCount);

        // 测试按类型计数
        $qb = $repository->createQueryBuilder('r');
        $aTypeCount = $qb->select('COUNT(r.id)')
            ->where('r.type = :type')
            ->setParameter('type', DnsRecordType::A)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(2, $aTypeCount);

        // 测试按有效性计数
        $qb = $repository->createQueryBuilder('r');
        $validCount = $qb->select('COUNT(r.id)')
            ->where('r.valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(2, $validCount);

        // 测试关联查询计数
        $qb = $repository->createQueryBuilder('r');
        $domainRecordCount = $qb->select('COUNT(r.id)')
            ->join('r.domain', 'd')
            ->where('d.name = :domainName')
            ->setParameter('domainName', 'count-test.com')
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(3, $domainRecordCount);

        // 测试组合条件计数
        $qb = $repository->createQueryBuilder('r');
        $combinedCount = $qb->select('COUNT(r.id)')
            ->where('r.type = :type')
            ->andWhere('r.valid = :valid')
            ->setParameter('type', DnsRecordType::A)
            ->setParameter('valid', true)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(1, $combinedCount);
    }

    public function testQueryBuilderMethods(): void
    {
        $repository = $this->getDnsRecordRepository();

        // 测试 createQueryBuilder
        $qb = $repository->createQueryBuilder('r');
        $this->assertInstanceOf('Doctrine\ORM\QueryBuilder', $qb);

        // 测试基本查询构建
        $query = $qb->select('r')
            ->where('r.valid = :valid')
            ->setParameter('valid', true)
            ->getQuery()
        ;
        $this->assertInstanceOf('Doctrine\ORM\Query', $query);

        // 测试复杂查询构建
        $qb = $repository->createQueryBuilder('r');
        $complexQuery = $qb
            ->select('r', 'd')
            ->join('r.domain', 'd')
            ->where('r.type = :type')
            ->andWhere('d.valid = :domainValid')
            ->orderBy('r.name', 'ASC')
            ->setParameter('type', DnsRecordType::A)
            ->setParameter('domainValid', true)
            ->getQuery()
        ;
        $this->assertInstanceOf('Doctrine\ORM\Query', $complexQuery);
    }

    public function testGetEntityName(): void
    {
        $repository = $this->getDnsRecordRepository();
        $this->assertEquals(DnsRecord::class, $repository->getClassName());
    }

    public function testGetClassName(): void
    {
        $repository = $this->getDnsRecordRepository();
        $this->assertEquals(DnsRecord::class, $repository->getClassName());
    }

    public function testComplexQueryScenarios(): void
    {
        // 清空数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsRecordRepository();

        // 创建复杂测试场景数据
        $account = new Account();
        $account->setName('复杂查询测试账户');
        $account->setSecretId('complex-test-id');
        $account->setSecretKey('complex-test-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('complex-test.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        // 创建不同类型和TTL的记录
        $records = [
            ['name' => 'www', 'value' => '192.168.1.1', 'type' => DnsRecordType::A, 'ttl' => 300, 'valid' => true],
            ['name' => 'api', 'value' => '192.168.1.2', 'type' => DnsRecordType::A, 'ttl' => 600, 'valid' => true],
            ['name' => 'blog', 'value' => '192.168.1.3', 'type' => DnsRecordType::A, 'ttl' => 300, 'valid' => false],
            ['name' => 'mail', 'value' => 'mail.example.com', 'type' => DnsRecordType::CNAME, 'ttl' => 3600, 'valid' => true],
            ['name' => 'ftp', 'value' => 'ftp.example.com', 'type' => DnsRecordType::CNAME, 'ttl' => null, 'valid' => true],
        ];

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);

        foreach ($records as $recordData) {
            $record = new DnsRecord();
            $record->setDomain($domain);
            $record->setName($recordData['name']);
            $record->setValue($recordData['value']);
            $record->setType($recordData['type']);
            $record->setTtl($recordData['ttl']);
            $record->setValid($recordData['valid']);
            self::getEntityManager()->persist($record);
        }
        self::getEntityManager()->flush();

        // 场景1：查找所有有效的A记录，按TTL排序
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $validARecords */
        $validARecords = $qb
            ->where('r.type = :type')
            ->andWhere('r.valid = :valid')
            ->orderBy('r.ttl', 'ASC')
            ->setParameter('type', DnsRecordType::A)
            ->setParameter('valid', true)
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(2, $validARecords);
        $this->assertEquals('www', $validARecords[0]->getName()); // TTL 300
        $this->assertEquals('api', $validARecords[1]->getName()); // TTL 600

        // 场景2：查找TTL在某个范围内的记录
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $ttlRangeRecords */
        $ttlRangeRecords = $qb
            ->where('r.ttl BETWEEN :minTtl AND :maxTtl')
            ->setParameter('minTtl', 300)
            ->setParameter('maxTtl', 1000)
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(3, $ttlRangeRecords);

        // 场景3：使用LIKE查询记录名称
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $namePatternRecords */
        $namePatternRecords = $qb
            ->where('r.name LIKE :pattern')
            ->setParameter('pattern', '%a%')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(2, $namePatternRecords); // api, mail

        // 场景4：查找每种类型的记录数量
        $qb = $repository->createQueryBuilder('r');
        /** @var list<array{recordType: DnsRecordType, recordCount: int}> $typeStats */
        $typeStats = $qb
            ->select('r.type as recordType', 'COUNT(r.id) as recordCount')
            ->groupBy('r.type')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(2, $typeStats);

        // 场景5：查找没有设置TTL的记录
        $qb = $repository->createQueryBuilder('r');
        /** @var list<DnsRecord> $noTtlRecords */
        $noTtlRecords = $qb
            ->where('r.ttl IS NULL')
            ->getQuery()
            ->getResult()
        ;
        $this->assertCount(1, $noTtlRecords);
        $this->assertEquals('ftp', $noTtlRecords[0]->getName());
    }

    // 标准 Repository 测试方法

    // 关联查询测试方法

    public function testFindOneByAssociationDomainShouldReturnMatchingEntity(): void
    {
        $repository = $this->getDnsRecordRepository();

        // 创建测试数据
        $account = new Account();
        $account->setName('关联查询测试账户');
        $account->setSecretId('association-find-id');
        $account->setSecretKey('association-find-key');
        $account->setValid(true);

        $domain = new DnsDomain();
        $domain->setName('association-find.com');
        $domain->setAccount($account);
        $domain->setValid(true);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('association-test');
        $record->setValue('192.168.1.100');
        $record->setType(DnsRecordType::A);
        $record->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain);
        self::getEntityManager()->persist($record);
        self::getEntityManager()->flush();

        $result = $repository->findOneBy(['domain' => $domain]);
        $this->assertInstanceOf(DnsRecord::class, $result);
        $this->assertNotNull($result->getDomain());
        $this->assertEquals($domain->getId(), $result->getDomain()->getId());
    }

    public function testCountByAssociationDomainShouldReturnCorrectNumber(): void
    {
        // 清空数据
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsRecord')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\DnsDomain')->execute();
        self::getEntityManager()->createQuery('DELETE FROM TencentCloudDnsBundle\Entity\Account')->execute();

        $repository = $this->getDnsRecordRepository();

        // 创建测试数据
        $account = new Account();
        $account->setName('关联计数测试账户');
        $account->setSecretId('association-count-id');
        $account->setSecretKey('association-count-key');
        $account->setValid(true);

        $domain1 = new DnsDomain();
        $domain1->setName('association-count1.com');
        $domain1->setAccount($account);
        $domain1->setValid(true);

        $domain2 = new DnsDomain();
        $domain2->setName('association-count2.com');
        $domain2->setAccount($account);
        $domain2->setValid(true);

        // Domain1 有2条记录，Domain2 有1条记录
        $record1 = new DnsRecord();
        $record1->setDomain($domain1);
        $record1->setName('d1-record1');
        $record1->setValue('192.168.1.1');
        $record1->setType(DnsRecordType::A);
        $record1->setValid(true);

        $record2 = new DnsRecord();
        $record2->setDomain($domain1);
        $record2->setName('d1-record2');
        $record2->setValue('192.168.1.2');
        $record2->setType(DnsRecordType::A);
        $record2->setValid(true);

        $record3 = new DnsRecord();
        $record3->setDomain($domain2);
        $record3->setName('d2-record1');
        $record3->setValue('192.168.2.1');
        $record3->setType(DnsRecordType::A);
        $record3->setValid(true);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->persist($domain1);
        self::getEntityManager()->persist($domain2);
        self::getEntityManager()->persist($record1);
        self::getEntityManager()->persist($record2);
        self::getEntityManager()->persist($record3);
        self::getEntityManager()->flush();

        $domain1Count = $repository->count(['domain' => $domain1]);
        $this->assertEquals(2, $domain1Count);

        $domain2Count = $repository->count(['domain' => $domain2]);
        $this->assertEquals(1, $domain2Count);
    }

    // 可空字段 IS NULL 测试方法

    // 健壮性测试（异常处理）
}
