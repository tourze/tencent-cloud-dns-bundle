<?php

namespace TencentCloudDnsBundle\Service;

use Pdp\Domain;
use Pdp\TopLevelDomains;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Dnspod\V20210323\DnspodClient;
use TencentCloud\Dnspod\V20210323\Models\CreateRecordRequest;
use TencentCloud\Dnspod\V20210323\Models\DeleteRecordRequest;
use TencentCloud\Dnspod\V20210323\Models\ModifyRecordRequest;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;

class DnsService
{
    public function __construct(
        private readonly TopLevelDomains $topLevelDomains,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function getDnsPodDNS(DnsDomain $domain): DnspodClient
    {
        if (!$domain->getAccount()) {
            throw new \RuntimeException('域名未绑定账号');
        }

        // 实例化一个认证对象，入参需要传入腾讯云账户 SecretId 和 SecretKey，此处还需注意密钥对的保密
        // 代码泄露可能会导致 SecretId 和 SecretKey 泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议采用更安全的方式来使用密钥，请参见：https://cloud.tencent.com/document/product/1278/85305
        // 密钥可前往官网控制台 https://console.cloud.tencent.com/cam/capi 进行获取
        $cred = new Credential($domain->getAccount()->getSecretId(), $domain->getAccount()->getSecretKey());
        // 实例化一个http选项，可选的，没有特殊需求可以跳过
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint('dnspod.tencentcloudapi.com');

        // 实例化一个client选项，可选的，没有特殊需求可以跳过
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);

        // 实例化要请求产品的client对象,clientProfile是可选的
        return new DnspodClient($cred, '', $clientProfile);
    }

    /**
     * 创建DNS记录
     */
    public function createRecord(DnsRecord $record): void
    {
        $domain = $record->getDomain();

        $dnsService = $this->getDnsPodDNS($domain);

        // 实例化一个请求对象,每个接口都会对应一个request对象
        // https://console.cloud.tencent.com/api/explorer?Product=dnspod&Version=2021-03-23&Action=ModifyRecord
        $req = new CreateRecordRequest();

        $parseDomain = Domain::fromIDNA2008($record->getRecord());
        $result = $this->topLevelDomains->resolve($parseDomain);

        $req->Domain = $domain->getName();
        $req->SubDomain = $result->subDomain()->toString();
        $req->RecordType = DnsRecordType::A->value;
        $req->RecordLine = 'RecordLine';
        $req->Value = $record->getValue();
        $req->TTL = $record->getTtl();
        $req->Weight = 0;
        $req->Status = 'ENABLE';

        // 返回的resp是一个CreateRecordResponse的实例，与请求对象对应
        $resp = $dnsService->CreateRecord($req);
        $this->logger->info('更新DnsPod域名记录成功', [
            'record' => $record,
            'resp' => $resp,
        ]);

        $record->setRecordId($resp->RecordId);
    }

    /**
     * 同步DNS记录到远程服务器
     */
    public function updateRecord(DnsRecord $record): void
    {
        $domain = $record->getDomain();

        $dnsService = $this->getDnsPodDNS($domain);

        // 实例化一个请求对象,每个接口都会对应一个request对象
        // https://console.cloud.tencent.com/api/explorer?Product=dnspod&Version=2021-03-23&Action=ModifyRecord
        $req = new ModifyRecordRequest();

        $parseDomain = Domain::fromIDNA2008($record->getRecord());
        $result = $this->topLevelDomains->resolve($parseDomain);

        $req->Domain = $domain->getName();
        $req->SubDomain = $result->subDomain()->toString();
        $req->RecordType = $record->getType()->value;
        $req->RecordLine = '默认';
        $req->Value = $record->getValue();
        $req->TTL = $record->getTtl();
        $req->Weight = 0;
        $req->Status = 'ENABLE';

        // 返回的resp是一个ModifyRecordResponse的实例，与请求对象对应
        $resp = $dnsService->ModifyRecord($req);
        $this->logger->info('更新DnsPod域名记录成功', [
            'record' => $record,
            'resp' => $resp,
        ]);
    }

    /**
     * 删除DNS记录
     */
    public function removeRecord(DnsRecord $record): void
    {
        $domain = $record->getDomain();

        $client = $this->getDnsPodDNS($domain);
        if ($record->getRecordId()) {
            $req = new DeleteRecordRequest();

            $req->Domain = $domain->getName();
            $req->RecordId = $record->getRecordId();

            // 返回的resp是一个DeleteRecordResponse的实例，与请求对象对应
            $resp = $client->DeleteRecord($req);
            $this->logger->info('删除DnsPod域名记录成功', [
                'record' => $record,
                'resp' => $resp,
            ]);
        }
    }
}
