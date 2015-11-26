<?php

namespace Tests\Homie\Client\LocalClient;

use Monolog\Logger;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Client\LocalClient;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * @covers Homie\Client\LocalClient
 */
class LocalClientTest extends PHPUnit_Framework_TestCase
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
        $this->processBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->logger = $this->getMock(Logger::class, [], [], '', false);

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

        $process = $this->getMock(Process::class, [], [], '', false);

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

        $this->logger
            ->expects($this->at(0))
            ->method('log')
            ->with(LogLevel::INFO, 'LocalClient command: command [foo]');

        $this->subject->executeWithReturn($command, ['foo']);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->getMock(Process::class, [], [], '', false);

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
            ->with(LogLevel::INFO, 'LocalClient command: command []');

        $this->logger
            ->expects($this->at(1))
            ->method('log')
            ->with(LogLevel::DEBUG, 'LocalClient command output: command []: output');

        $actualResult = $this->subject->executeWithReturn($command);
        $this->assertEquals($output, $actualResult);
    }

    public function testExecute()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->getMock(Process::class, [], [], '', false);

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
            ->with(LogLevel::INFO, 'LocalClient command: command []');

        $this->logger
            ->expects($this->at(1))
            ->method('log')
            ->with(LogLevel::DEBUG, 'LocalClient command output: command []: output');

        $this->subject->execute($command);
    }
}
