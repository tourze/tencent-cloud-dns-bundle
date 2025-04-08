<?php

namespace TencentCloudDnsBundle\Service;

use Pdp\TopLevelDomains;

class DomainParserFactory
{
    public function createIANATopLevelDomainListParser(): TopLevelDomains
    {
        return TopLevelDomains::fromPath(__DIR__ . '/../Resources/data/tlds-alpha-by-domain.txt');
    }
}
