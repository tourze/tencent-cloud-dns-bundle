<?php

namespace TencentCloudDnsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TencentCloudDnsBundle\DependencyInjection\TencentCloudDnsExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(TencentCloudDnsExtension::class)]
final class TencentCloudDnsExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testLoad(): void
    {
        $extension = new TencentCloudDnsExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'test');

        $configs = [];

        $extension->load($configs, $container);

        // 验证服务配置被加载
        $this->assertNotEmpty($container->getDefinitions());
    }

    public function testConfigurationDirectoryExists(): void
    {
        // 测试配置目录的存在性，避免直接调用受保护的方法
        $bundleReflection = new \ReflectionClass(TencentCloudDnsExtension::class);
        $fileName = $bundleReflection->getFileName();
        $this->assertIsString($fileName);
        $bundleDir = dirname($fileName, 2);
        $configDir = $bundleDir . '/Resources/config';

        $this->assertDirectoryExists($configDir);
        $this->assertStringContainsString('Resources/config', $configDir);
    }
}
