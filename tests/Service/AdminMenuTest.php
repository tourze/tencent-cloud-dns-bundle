<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * AdminMenu 菜单服务测试
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Tests don't require special setup
    }

    public function testServiceIsCallable(): void
    {
        $service = self::getService(AdminMenu::class);
        // 验证服务实现了 __invoke 方法
        $reflection = new \ReflectionClass($service);
        $this->assertTrue($reflection->hasMethod('__invoke'));
        $this->assertTrue($reflection->getMethod('__invoke')->isPublic());
    }

    public function testInvokeCreatesTencentCloudDnsMenu(): void
    {
        $service = self::getService(AdminMenu::class);
        $rootMenu = $this->createMock(ItemInterface::class);
        $dnsMenu = $this->createMock(ItemInterface::class);

        // 第一次调用返回null（不存在），第二次调用返回子菜单对象
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('腾讯云DNS')
            ->willReturnOnConsecutiveCalls(null, $dnsMenu)
        ;

        $rootMenu->expects($this->once())
            ->method('addChild')
            ->with('腾讯云DNS')
            ->willReturn($dnsMenu)
        ;

        // 设置子菜单的添加期望 - 总共3个菜单项：账号管理、域名管理、记录管理
        $dnsMenu->expects($this->exactly(3))
            ->method('addChild')
            ->willReturnCallback(function () {
                return $this->createMock(ItemInterface::class);
            })
        ;

        $service->__invoke($rootMenu);
    }

    public function testInvokeHandlesExistingTencentCloudDnsMenu(): void
    {
        $service = self::getService(AdminMenu::class);
        $rootMenu = $this->createMock(ItemInterface::class);
        $dnsMenu = $this->createMock(ItemInterface::class);

        // 第一次和第二次调用都返回已存在的子菜单
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('腾讯云DNS')
            ->willReturn($dnsMenu)
        ;

        $rootMenu->expects($this->never())
            ->method('addChild')
        ;

        // 设置子菜单的添加期望 - 总共3个菜单项：账号管理、域名管理、记录管理
        $dnsMenu->expects($this->exactly(3))
            ->method('addChild')
            ->willReturnCallback(function () {
                return $this->createMock(ItemInterface::class);
            })
        ;

        $service->__invoke($rootMenu);
    }

    public function testInvokeReturnEarlyWhenChildMenuIsNull(): void
    {
        $service = self::getService(AdminMenu::class);
        $rootMenu = $this->createMock(ItemInterface::class);

        // 第一次调用返回null，第二次也返回null（表示添加失败）
        $rootMenu->expects($this->exactly(2))
            ->method('getChild')
            ->with('腾讯云DNS')
            ->willReturn(null)
        ;

        $rootMenu->expects($this->once())
            ->method('addChild')
            ->with('腾讯云DNS')
            ->willReturn($this->createMock(ItemInterface::class))
        ;

        $service->__invoke($rootMenu);
    }
}
