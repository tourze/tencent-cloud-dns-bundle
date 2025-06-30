<?php

namespace TencentCloudDnsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TencentCloudDnsBundle\DependencyInjection\TencentCloudDnsExtension;

class TencentCloudDnsExtensionTest extends TestCase
{
    private TencentCloudDnsExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new TencentCloudDnsExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $configs = [];
        
        $this->extension->load($configs, $this->container);

        // 验证服务配置被加载
        $this->assertNotEmpty($this->container->getDefinitions());
        
        // 验证工厂服务
        $this->assertTrue($this->container->hasDefinition('Pdp\TopLevelDomains'));
    }
}