<?php

namespace TencentCloudDnsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudDnsBundle\Command\SyncDomainRecordToLocalCommand;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncDomainRecordToLocalCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncDomainRecordToLocalCommandTest extends AbstractCommandTestCase
{
    private object $command;

    private CommandTester $commandTester;

    protected function onSetUp(): void
    {
        $command = self::getContainer()->get(SyncDomainRecordToLocalCommand::class);
        $this->assertInstanceOf(SyncDomainRecordToLocalCommand::class, $command);
        $this->command = $command;

        $application = new Application();
        $application->add($this->command);

        $this->commandTester = new CommandTester($this->command);
    }

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    public function testExecuteWithNoDomains(): void
    {
        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
