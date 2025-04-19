<?php

namespace TencentCloudDnsBundle\Tests\Service;

use Pdp\TopLevelDomains;
use PHPUnit\Framework\TestCase;
use TencentCloudDnsBundle\Service\DomainParserFactory;

class DomainParserFactoryTest extends TestCase
{
    private DomainParserFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new DomainParserFactory();
    }

    public function testCreateIANATopLevelDomainListParser(): void
    {
        $parser = $this->factory->createIANATopLevelDomainListParser();

        $this->assertInstanceOf(TopLevelDomains::class, $parser);

        // 测试解析常见域名
        $domain = \Pdp\Domain::fromIDNA2008('example.com');
        $result = $parser->resolve($domain);

        $this->assertNotNull($result);
        $this->assertEquals('com', $result->suffix()->toString());
        $this->assertEquals('example.com', $result->domain()->toString());
    }
}
