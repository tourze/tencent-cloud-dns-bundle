<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use TencentCloudDnsBundle\Enum\DnsRecordType;

#[When(env: 'test')]
#[When(env: 'dev')]
class DnsRecordFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const A_RECORD_REFERENCE = 'a-record';
    public const CNAME_RECORD_REFERENCE = 'cname-record';

    public static function getGroups(): array
    {
        return [
            'tencent_cloud_dns',
            'tencent_cloud_dns_record',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $mainDomain = $this->getReference(DnsDomainFixtures::MAIN_DOMAIN_REFERENCE, DnsDomain::class);
        $testDomain = $this->getReference(DnsDomainFixtures::TEST_DOMAIN_REFERENCE, DnsDomain::class);

        $aRecord = new DnsRecord();
        $aRecord->setDomain($mainDomain);
        $aRecord->setName('www');
        $aRecord->setValue('192.168.1.100');
        $aRecord->setType(DnsRecordType::A);
        $aRecord->setTtl(3600);
        $aRecord->setRecordId('record123');
        $aRecord->setContext(['priority' => 10, 'weight' => 5]);
        $aRecord->setValid(true);
        $aRecord->setCreateTime(CarbonImmutable::now()->modify('-20 days'));
        $aRecord->setUpdateTime(CarbonImmutable::now()->modify('-2 days'));
        $manager->persist($aRecord);
        $this->addReference(self::A_RECORD_REFERENCE, $aRecord);

        $cnameRecord = new DnsRecord();
        $cnameRecord->setDomain($mainDomain);
        $cnameRecord->setName('api');
        $cnameRecord->setValue('www.main-domain.test');
        $cnameRecord->setType(DnsRecordType::CNAME);
        $cnameRecord->setTtl(1800);
        $cnameRecord->setRecordId('record456');
        $cnameRecord->setContext(['alias' => true]);
        $cnameRecord->setValid(true);
        $cnameRecord->setCreateTime(CarbonImmutable::now()->modify('-18 days'));
        $cnameRecord->setUpdateTime(CarbonImmutable::now()->modify('-1 day'));
        $manager->persist($cnameRecord);
        $this->addReference(self::CNAME_RECORD_REFERENCE, $cnameRecord);

        $txtRecord = new DnsRecord();
        $txtRecord->setDomain($testDomain);
        $txtRecord->setName('_verification');
        $txtRecord->setValue('verification-code-12345');
        $txtRecord->setType(DnsRecordType::TXT);
        $txtRecord->setTtl(600);
        $txtRecord->setRecordId('record789');
        $txtRecord->setContext(['verification_type' => 'domain_ownership']);
        $txtRecord->setValid(false);
        $txtRecord->setCreateTime(CarbonImmutable::now()->modify('-10 days'));
        $txtRecord->setUpdateTime(CarbonImmutable::now()->modify('-5 hours'));
        $manager->persist($txtRecord);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DnsDomainFixtures::class,
        ];
    }
}
