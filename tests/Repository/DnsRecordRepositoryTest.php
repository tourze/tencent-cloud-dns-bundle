<?php

namespace TencentCloudDnsBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Repository\DnsRecordRepository;

class DnsRecordRepositoryTest extends TestCase
{
    private ManagerRegistry&MockObject $registry;
    private EntityManagerInterface&MockObject $entityManager;
    private DnsRecordRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $classMetadata = new ClassMetadata(DnsRecord::class);
        
        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(DnsRecord::class)
            ->willReturn($classMetadata);
        
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(DnsRecord::class)
            ->willReturn($this->entityManager);
        
        $this->repository = new DnsRecordRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(DnsRecordRepository::class, $this->repository);
    }
}