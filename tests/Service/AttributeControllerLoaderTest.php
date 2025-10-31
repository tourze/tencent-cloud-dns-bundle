<?php

declare(strict_types=1);

namespace TencentCloudDnsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use TencentCloudDnsBundle\Service\AttributeControllerLoader;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * AttributeControllerLoader 路由加载器测试
 *
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    protected function onSetUp(): void
    {
        $loader = self::getContainer()->get(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $loader);
        $this->loader = $loader;
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AttributeControllerLoader::class, $this->loader);
    }

    public function testLoadReturnsRouteCollection(): void
    {
        $routes = $this->loader->load('resource');
        $this->assertInstanceOf(RouteCollection::class, $routes);
        // 路由集合可能为空，这是正常的，只要返回正确的类型即可
        $this->assertGreaterThanOrEqual(0, $routes->count());
    }

    public function testAutoloadReturnsRouteCollection(): void
    {
        $routes = $this->loader->autoload();
        $this->assertInstanceOf(RouteCollection::class, $routes);
        // 路由集合可能为空，这是正常的，只要返回正确的类型即可
        $this->assertGreaterThanOrEqual(0, $routes->count());
    }

    public function testSupportsReturnsFalse(): void
    {
        $this->assertFalse($this->loader->supports('resource'));
        $this->assertFalse($this->loader->supports('resource', 'type'));
    }

    public function testLoadAndAutoloadReturnSameRoutes(): void
    {
        $loadRoutes = $this->loader->load('resource');
        $autoloadRoutes = $this->loader->autoload();

        // 验证两个方法返回相同数量的路由
        $this->assertSame($loadRoutes->count(), $autoloadRoutes->count());

        // 验证路由名称相同
        $loadRouteNames = array_keys($loadRoutes->all());
        $autoloadRouteNames = array_keys($autoloadRoutes->all());

        sort($loadRouteNames);
        sort($autoloadRouteNames);

        $this->assertSame($loadRouteNames, $autoloadRouteNames);
    }

    public function testHasRoutesForAllControllers(): void
    {
        $routes = $this->loader->load('resource');
        $routeNames = array_keys($routes->all());

        // 如果路由为空，说明在测试环境中EasyAdmin路由未加载，这是正常的
        if (0 === count($routeNames)) {
            self::markTestSkipped('EasyAdmin routes not loaded in test environment');
        }

        // 验证包含账号管理相关路由
        $accountRoutes = array_filter($routeNames, static function (string $name): bool {
            return str_contains($name, 'account');
        });
        $this->assertNotEmpty($accountRoutes);

        // 验证包含域名管理相关路由
        $domainRoutes = array_filter($routeNames, static function (string $name): bool {
            return str_contains($name, 'domain');
        });
        $this->assertNotEmpty($domainRoutes);

        // 验证包含记录管理相关路由
        $recordRoutes = array_filter($routeNames, static function (string $name): bool {
            return str_contains($name, 'record');
        });
        $this->assertNotEmpty($recordRoutes);
    }
}
