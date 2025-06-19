<?php

namespace TencentCloudDnsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TencentCloud\Dnspod\V20210323\Models\DescribeRecordListRequest;
use TencentCloud\Dnspod\V20210323\Models\RecordListItem;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;
use TencentCloudDnsBundle\Repository\DnsDomainRepository;
use TencentCloudDnsBundle\Repository\DnsRecordRepository;
use TencentCloudDnsBundle\Service\DnsService;

#[AsCommand(name: self::NAME, description: '同步域名信息到本地')]
class SyncDomainRecordToLocalCommand extends Command
{
    public const NAME = 'tencent-cloud-dns:sync-domain-record-to-local';
    public function __construct(
        private readonly DnsDomainRepository $domainRepository,
        private readonly DnsRecordRepository $recordRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly DnsService $dnsService,
    ) {
        parent::__construct();
    }

    private function syncDomain(DnsDomain $domain, OutputInterface $output): void
    {
        $output->writeln("开始处理域名：{$domain->getName()}");

        $client = $this->dnsService->getDnsPodDNS($domain);

        // 实例化一个请求对象,每个接口都会对应一个request对象
        $req = new DescribeRecordListRequest();
        $req->Domain = $domain->getName();

        // 返回的resp是一个DescribeRecordListResponse的实例，与请求对象对应
        $resp = $client->DescribeRecordList($req);

        foreach ($resp->getRecordList() as $item) {
            /** @var RecordListItem $item */
            $record = $this->recordRepository->findOneBy([
                'domain' => $domain,
                'recordId' => $item->getRecordId(),
            ]);
            if (!$record) {
                $record = new DnsRecord();
                $record->setDomain($domain);
                $record->setRecordId($item->getRecordId());
            }
            $record->setName($item->getName());
            $record->setType(DnsRecordType::tryFrom($item->getType()));
            $record->setValue($item->getValue());
            $record->setTtl($item->getTTL());
            $this->entityManager->persist($record);
            $this->entityManager->flush();
            $output->writeln("发现子域名：{$record->getName()}");
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->domainRepository->findAll() as $domain) {
            try {
                $this->syncDomain($domain, $output);
            } catch (\Throwable $exception) {
                $this->logger->error('同步DNS记录失败', [
                    'domain' => $domain,
                    'exception' => $exception,
                ]);
            } finally {
                $output->writeln('');
            }
        }

        return Command::SUCCESS;
    }
}
