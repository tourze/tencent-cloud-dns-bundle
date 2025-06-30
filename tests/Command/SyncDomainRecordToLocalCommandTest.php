<?php

namespace TencentCloudDnsBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudDnsBundle\Command\SyncDomainRecordToLocalCommand;
use TencentCloudDnsBundle\Repository\DnsDomainRepository;
use TencentCloudDnsBundle\Repository\DnsRecordRepository;
use TencentCloudDnsBundle\Service\DnsService;

class SyncDomainRecordToLocalCommandTest extends TestCase
{
    private DnsDomainRepository&MockObject $domainRepository;
    private DnsRecordRepository&MockObject $recordRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private LoggerInterface&MockObject $logger;
    private DnsService&MockObject $dnsService;
    private SyncDomainRecordToLocalCommand $command;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->domainRepository = $this->createMock(DnsDomainRepository::class);
        $this->recordRepository = $this->createMock(DnsRecordRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->dnsService = $this->createMock(DnsService::class);
        
        $this->command = new SyncDomainRecordToLocalCommand(
            $this->domainRepository,
            $this->recordRepository,
            $this->entityManager,
            $this->logger,
            $this->dnsService
        );

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecuteWithNoDomains(): void
    {
        $this->domainRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}