<?php

namespace TencentCloudDnsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Service\SdkService;

class SdkServiceTest extends TestCase
{
    private SdkService $sdkService;
    private Account $account;

    protected function setUp(): void
    {
        $this->sdkService = new SdkService();

        $this->account = new Account();
        $this->account->setSecretId('test-secret-id');
        $this->account->setSecretKey('test-secret-key');
    }

    public function testGetCredential(): void
    {
        $credential = $this->sdkService->getCredential($this->account);

        $this->assertInstanceOf(Credential::class, $credential);
    }

    public function testGetHttpProfile(): void
    {
        // 不传入参数的情况
        $httpProfile = $this->sdkService->getHttpProfile();
        $this->assertInstanceOf(HttpProfile::class, $httpProfile);

        // 传入自定义 endpoint 的情况
        $endpoint = 'custom-endpoint.example.com';
        $httpProfileWithEndpoint = $this->sdkService->getHttpProfile($endpoint);
        $this->assertInstanceOf(HttpProfile::class, $httpProfileWithEndpoint);

        // 使用反射检查 endpoint 是否设置正确
        $reflection = new \ReflectionObject($httpProfileWithEndpoint);
        $endpointProperty = $reflection->getProperty('endpoint');
        $endpointProperty->setAccessible(true);
        $this->assertEquals($endpoint, $endpointProperty->getValue($httpProfileWithEndpoint));
    }

    public function testGetClientProfile(): void
    {
        // 不传入 HttpProfile 参数的情况
        $clientProfile = $this->sdkService->getClientProfile();
        $this->assertInstanceOf(ClientProfile::class, $clientProfile);

        // 传入 HttpProfile 的情况
        $httpProfile = new HttpProfile();
        $clientProfileWithHttpProfile = $this->sdkService->getClientProfile($httpProfile);
        $this->assertInstanceOf(ClientProfile::class, $clientProfileWithHttpProfile);

        // 使用反射检查 httpProfile 是否设置正确
        $reflection = new \ReflectionObject($clientProfileWithHttpProfile);
        $profileProperty = $reflection->getProperty('httpProfile');
        $profileProperty->setAccessible(true);
        $this->assertSame($httpProfile, $profileProperty->getValue($clientProfileWithHttpProfile));
    }
}
