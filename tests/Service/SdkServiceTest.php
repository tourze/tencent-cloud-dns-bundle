<?php

namespace TencentCloudDnsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloudDnsBundle\Entity\Account;
use TencentCloudDnsBundle\Service\SdkService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SdkService::class)]
#[RunTestsInSeparateProcesses]
final class SdkServiceTest extends AbstractIntegrationTestCase
{
    private SdkService $sdkService;

    protected function onSetUp(): void
    {
        $sdkService = self::getContainer()->get(SdkService::class);
        $this->assertInstanceOf(SdkService::class, $sdkService);
        $this->sdkService = $sdkService;
    }

    public function testGetCredential(): void
    {
        $account = new Account();
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $credential = $this->sdkService->getCredential($account);

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
