<?php

namespace TencentCloudDnsBundle\Tests\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Repository\AccountRepository;

class AccountRepositoryTest extends TestCase
{
    private ManagerRegistry&MockObject $registry;
    private EntityManagerInterface&MockObject $entityManager;
    private AccountRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $classMetadata = new ClassMetadata(Account::class);
        
        $this->entityManager->expects($this->any())
            ->method('getClassMetadata')
            ->with(Account::class)
            ->willReturn($classMetadata);
        
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(Account::class)
            ->willReturn($this->entityManager);
        
        $this->repository = new AccountRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(AccountRepository::class, $this->repository);
    }
}