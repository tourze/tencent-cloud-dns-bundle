<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use TencentCloudDnsBundle\Entity\Account;

#[When(env: 'test')]
#[When(env: 'dev')]
class AccountFixtures extends Fixture implements FixtureGroupInterface
{
    public const TENCENT_CLOUD_ACCOUNT_REFERENCE = 'tencent-cloud-account';
    public const SECONDARY_ACCOUNT_REFERENCE = 'secondary-account';

    public static function getGroups(): array
    {
        return [
            'tencent_cloud_dns',
            'tencent_cloud_dns_account',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('主账号');
        $account->setSecretId('AKID' . bin2hex(random_bytes(16)));
        $account->setSecretKey(bin2hex(random_bytes(20)));
        $account->setValid(true);
        $account->setCreateTime(CarbonImmutable::now()->modify('-30 days'));
        $account->setUpdateTime(CarbonImmutable::now()->modify('-5 days'));
        $manager->persist($account);
        $this->addReference(self::TENCENT_CLOUD_ACCOUNT_REFERENCE, $account);

        $secondaryAccount = new Account();
        $secondaryAccount->setName('备用账号');
        $secondaryAccount->setSecretId('AKID' . bin2hex(random_bytes(16)));
        $secondaryAccount->setSecretKey(bin2hex(random_bytes(20)));
        $secondaryAccount->setValid(false);
        $secondaryAccount->setCreateTime(CarbonImmutable::now()->modify('-20 days'));
        $secondaryAccount->setUpdateTime(CarbonImmutable::now()->modify('-2 days'));
        $manager->persist($secondaryAccount);
        $this->addReference(self::SECONDARY_ACCOUNT_REFERENCE, $secondaryAccount);

        $manager->flush();
    }
}
