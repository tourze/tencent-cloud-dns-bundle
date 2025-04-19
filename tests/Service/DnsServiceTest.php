<?php

namespace TencentCloudDnsBundle\Tests\Service;

use Pdp\TopLevelDomains;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use TencentCloudDnsBundle\Service\DnsService;
use TencentCloudDnsBundle\Service\DomainParserFactory;

class DnsServiceTest extends TestCase
{
    private TopLevelDomains $topLevelDomains;

    /** @var LoggerInterface&MockObject */
    private $loggerMock;

    private Account $account;
    private DnsDomain $domain;
    private DnsRecord $record;

    protected function setUp(): void
    {
        // 使用真实的 TopLevelDomains 实例而不是模拟对象
        $factory = new DomainParserFactory();
        $this->topLevelDomains = $factory->createIANATopLevelDomainListParser();

        $this->loggerMock = $this->createMock(LoggerInterface::class);

        // 创建测试数据
        $this->account = new Account();
        $this->account->setName('测试账号');
        $this->account->setSecretId('test-secret-id');
        $this->account->setSecretKey('test-secret-key');

        $this->domain = new DnsDomain();
        $this->domain->setName('example.com');
        $this->domain->setAccount($this->account);

        $this->record = new DnsRecord();
        $this->record->setDomain($this->domain);
        $this->record->setName('test');
        $this->record->setValue('192.168.1.1');
        $this->record->setType(DnsRecordType::A);
        $this->record->setTtl(600);
    }

    public function testGetDnsPodDNSThrowsExceptionWhenNoAccount(): void
    {
        $domainWithoutAccount = new DnsDomain();
        $domainWithoutAccount->setName('example.com');

        $dnsService = new DnsService($this->topLevelDomains, $this->loggerMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('域名未绑定账号');

        $dnsService->getDnsPodDNS($domainWithoutAccount);
    }

    public function testGetDnsPodDNS(): void
    {
        // 这个测试在实际环境中需要真实的凭据，所以我们跳过它
        $this->markTestSkipped('需要真实外部依赖或更好的模拟方式');
    }
}
