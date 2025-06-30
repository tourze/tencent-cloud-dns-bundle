<?php

namespace TencentCloudDnsBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Repository\DnsDomainRepository;

class DnsDomainRepositoryTest extends TestCase
{
    private ManagerRegistry&MockObject $registry;
    private EntityManagerInterface&MockObject $entityManager;
    private DnsDomainRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $classMetadata = new ClassMetadata(DnsDomain::class);
        
        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(DnsDomain::class)
            ->willReturn($classMetadata);
        
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(DnsDomain::class)
            ->willReturn($this->entityManager);
        
        $this->repository = new DnsDomainRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(DnsDomainRepository::class, $this->repository);
    }
}