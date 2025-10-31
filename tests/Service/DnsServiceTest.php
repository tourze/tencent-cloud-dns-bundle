<?php

namespace TencentCloudDnsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use TencentCloudDnsBundle\Service\DnsService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DnsService::class)]
#[RunTestsInSeparateProcesses]
final class DnsServiceTest extends AbstractIntegrationTestCase
{
    private DnsService $dnsService;

    protected function onSetUp(): void
    {
        $dnsService = self::getContainer()->get(DnsService::class);
        $this->assertInstanceOf(DnsService::class, $dnsService);
        $this->dnsService = $dnsService;
    }

    public function testGetDnsPodDNSThrowsExceptionWhenNoAccount(): void
    {
        $domainWithoutAccount = new DnsDomain();
        $domainWithoutAccount->setName('example.com');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('域名未绑定账号');

        $this->dnsService->getDnsPodDNS($domainWithoutAccount);
    }

    public function testCreateRecord(): void
    {
        // 创建测试数据
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $domain = new DnsDomain();
        $domain->setName('example.com');
        $domain->setAccount($account);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('test');
        $record->setValue('192.168.1.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(600);

        // 预期会抛出异常，因为我们使用的是测试凭证
        $this->expectException(\Exception::class);
        $this->dnsService->createRecord($record);
    }

    public function testUpdateRecord(): void
    {
        // 创建测试数据
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $domain = new DnsDomain();
        $domain->setName('example.com');
        $domain->setAccount($account);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('test');
        $record->setValue('192.168.1.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(600);

        // 预期会抛出异常，因为我们使用的是测试凭证
        $this->expectException(\Exception::class);
        $this->dnsService->updateRecord($record);
    }

    public function testRemoveRecord(): void
    {
        // 创建测试数据
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $domain = new DnsDomain();
        $domain->setName('example.com');
        $domain->setAccount($account);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('test');
        $record->setValue('192.168.1.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(600);
        $record->setRecordId('12345');

        // 预期会抛出异常，因为我们使用的是测试凭证
        $this->expectException(\Exception::class);
        $this->dnsService->removeRecord($record);
    }

    public function testRemoveRecordWithoutRecordId(): void
    {
        // 创建测试数据
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $domain = new DnsDomain();
        $domain->setName('example.com');
        $domain->setAccount($account);

        $record = new DnsRecord();
        $record->setDomain($domain);
        $record->setName('test');
        $record->setValue('192.168.1.1');
        $record->setType(DnsRecordType::A);
        $record->setTtl(600);
        $record->setRecordId(null);

        // 当没有 recordId 时，应该不执行任何操作
        $this->dnsService->removeRecord($record);

        // 验证记录的 recordId 仍然为 null
        $this->assertNull($record->getRecordId());
    }
}
