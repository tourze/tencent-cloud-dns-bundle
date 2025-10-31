<?php

namespace TencentCloudDnsBundle\Tests\Service;

use Pdp\Domain;
use Pdp\TopLevelDomains;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudDnsBundle\Service\DomainParserFactory;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DomainParserFactory::class)]
#[RunTestsInSeparateProcesses]
final class DomainParserFactoryTest extends AbstractIntegrationTestCase
{
    private DomainParserFactory $factory;

    protected function onSetUp(): void
    {
        $factory = self::getContainer()->get(DomainParserFactory::class);
        $this->assertInstanceOf(DomainParserFactory::class, $factory);
        $this->factory = $factory;
    }

    public function testCreateIANATopLevelDomainListParser(): void
    {
        $parser = $this->factory->createIANATopLevelDomainListParser();

        $this->assertInstanceOf(TopLevelDomains::class, $parser);

        // 测试解析常见域名
        $domain = Domain::fromIDNA2008('example.com');
        $result = $parser->resolve($domain);

        $this->assertNotNull($result);
        $this->assertEquals('com', $result->suffix()->toString());
        $this->assertEquals('example.com', $result->domain()->toString());
    }
}
