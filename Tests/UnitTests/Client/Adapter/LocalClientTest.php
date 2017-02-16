<?php

namespace Tests\Homie\Adapter\Client;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\Adapter\LocalClient;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * @covers \Homie\Client\Adapter\LocalClient
 */
class LocalClientTest extends TestCase
{

    /**
     * @var ProcessBuilder|MockObject
     */
    private $processBuilder;

    /**
     * @var LocalClient
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->processBuilder = $this->createMock(ProcessBuilder::class);
        $this->logger = $this->createMock(Logger::class);

        $this->subject = new LocalClient($this->processBuilder);
        $this->subject->setLogger($this->logger);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage error
     */
    public function testExecuteWithReturnAndFail()
    {
        $command = 'command';

        $process = $this->createMock(Process::class);

        $this->processBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->with([$command])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with(['foo'])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $process
            ->expects($this->once())
            ->method('getErrorOutput')
            ->willReturn('error');
        $process
            ->expects($this->exactly(2))
            ->method('getCommandLine')
            ->willReturn('commandline');

        $this->logger
            ->expects($this->at(0))
            ->method('log')
            ->with(LogLevel::INFO, 'LocalClient command: commandline');

        $this->subject->executeWithReturn($command, ['foo']);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->createMock(Process::class);

        $this->processBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->with([$command])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with([])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('run');

        $process
            ->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $process
            ->expects($this->once())
            ->method('getOutput')
            ->willReturn($output);

        $this->logger
            ->expects($this->at(0))
            ->method('log')
            ->with(LogLevel::INFO, 'LocalClient command: commandline');
        $process
            ->expects($this->exactly(2))
            ->method('getCommandLine')
            ->willReturn('commandline');

        $this->logger
            ->expects($this->at(1))
            ->method('log')
            ->with(LogLevel::DEBUG, 'LocalClient command output: commandline: output');

        $actualResult = $this->subject->executeWithReturn($command);
        $this->assertEquals($output, $actualResult);
    }

    public function testExecute()
    {
        $command = 'command';
        $process = $this->createMock(Process::class);

        $this->processBuilder
            ->expects($this->once())
            ->method('setPrefix')
            ->with([$command])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with([])
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('start');

        $process
            ->expects($this->once())
            ->method('getCommandLine')
            ->willReturn('commandLine');

        $this->logger
            ->expects($this->at(0))
            ->method('log')
            ->with(LogLevel::INFO, 'LocalClient command: commandLine');

        $this->subject->execute($command);
    }
}
