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
        ->will($this->returnValue($process));

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
        ->will($this->returnValue(false));

        $process
        ->expects($this->once())
        ->method('getErrorOutput')
        ->will($this->returnValue('error'));

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
        ->will($this->returnValue($process));

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
        ->will($this->returnValue(true));

        $process
        ->expects($this->once())
        ->method('getOutput')
        ->will($this->returnValue($output));

        $actual_result = $this->subject->executeWithReturn($command);
        $this->assertEquals($output, $actual_result);
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
        ->will($this->returnValue($process));

        $process
        ->expects($this->once())
        ->method('run');

        $process
        ->expects($this->once())
        ->method('isSuccessful')
        ->will($this->returnValue(true));

        $process
        ->expects($this->once())
        ->method('getOutput')
        ->will($this->returnValue($output));

        $this->subject->execute($command);
    }
}
