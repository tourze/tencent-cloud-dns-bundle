<?php

namespace TencentCloudDnsBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class TencentCloudDnsExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
