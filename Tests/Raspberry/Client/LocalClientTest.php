<?php

namespace Tests\Raspberry\Client\LocalClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Client\LocalClient;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * @Covers Raspberry\Client\LocalClient
 */
class LocalClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    /**
     * @var LocalClient
     */
    private $subject;

    public function setUp()
    {
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);

        $this->subject = new LocalClient($this->mockProcessBuilder);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage error
     */
    public function testExecuteWithReturnAndFail()
    {
        $command = 'command';

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('setCommandLine')
            ->with($command);

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

        $this->subject->executeWithReturn($command);
    }

    public function testExecuteWithReturn()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process
            ->expects($this->once())
            ->method('setCommandLine')
            ->with($command);

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

        $actualResult = $this->subject->executeWithReturn($command);
        $this->assertEquals($output, $actualResult);
    }

    public function testExecute()
    {
        $command = 'command';
        $output  = 'output';
        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('add')
            ->willReturnSelf();

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setTimeout')
            ->with(LocalClient::TIMEOUT)
            ->willReturnSelf();

        $this->mockProcessBuilder
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

        $this->subject->execute($command);
    }
}
