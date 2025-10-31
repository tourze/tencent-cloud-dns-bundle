<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;

#[When(env: 'test')]
#[When(env: 'dev')]
class DnsDomainFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const MAIN_DOMAIN_REFERENCE = 'main-domain';
    public const TEST_DOMAIN_REFERENCE = 'test-domain';

    public static function getGroups(): array
    {
        return [
            'tencent_cloud_dns',
            'tencent_cloud_dns_domain',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::TENCENT_CLOUD_ACCOUNT_REFERENCE, Account::class);

        $mainDomain = new DnsDomain();
        $mainDomain->setName('main-domain.test');
        $mainDomain->setAccount($account);
        $mainDomain->setContext(['zone_id' => 'zone123', 'domain_type' => 'primary']);
        $mainDomain->setValid(true);
        $mainDomain->setCreateTime(CarbonImmutable::now()->modify('-25 days'));
        $mainDomain->setUpdateTime(CarbonImmutable::now()->modify('-3 days'));
        $manager->persist($mainDomain);
        $this->addReference(self::MAIN_DOMAIN_REFERENCE, $mainDomain);

        $testDomain = new DnsDomain();
        $testDomain->setName('secondary-domain.test');
        $testDomain->setAccount($account);
        $testDomain->setContext(['zone_id' => 'zone456', 'domain_type' => 'secondary']);
        $testDomain->setValid(false);
        $testDomain->setCreateTime(CarbonImmutable::now()->modify('-15 days'));
        $testDomain->setUpdateTime(CarbonImmutable::now()->modify('-1 day'));
        $manager->persist($testDomain);
        $this->addReference(self::TEST_DOMAIN_REFERENCE, $testDomain);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
