<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Entity\DnsDomain;
use TencentCloudDnsBundle\Entity\DnsRecord;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 腾讯云DNS菜单服务
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('腾讯云DNS')) {
            $item->addChild('腾讯云DNS');
        }

        $dnsMenu = $item->getChild('腾讯云DNS');
        if (null === $dnsMenu) {
            return;
        }

        // 账号管理菜单
        $dnsMenu->addChild('账号管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-cog')
        ;

        // 域名管理菜单
        $dnsMenu->addChild('域名管理')
            ->setUri($this->linkGenerator->getCurdListPage(DnsDomain::class))
            ->setAttribute('icon', 'fas fa-globe')
        ;

        // 记录管理菜单
        $dnsMenu->addChild('记录管理')
            ->setUri($this->linkGenerator->getCurdListPage(DnsRecord::class))
            ->setAttribute('icon', 'fas fa-server')
        ;
    }
}
