<?php

namespace Tests\Homie\Sensors\Sensors;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Sensors\TemperatureDHT11;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class TemperatureDHT11Test extends TestCase
{

    /**
     * @var TemperatureDHT11
     */
    private $subject;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $processBuilder;

    /**
     * @var Filesystem|MockObject
     */
    private $fileSystem;

    public function setUp()
    {
        $this->processBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->fileSystem     = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new TemperatureDHT11($this->processBuilder, $this->fileSystem, '/ada/');
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureDHT11::TYPE, $actualResult);
    }
    public function testGetValueWitInvalidOutput()
    {
        $pin = 3;

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->willReturn($this->processBuilder);

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process->expects($this->once())
            ->method('run');

        $process->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(false);

        $actualResult = $this->subject->getValue($pin);

        $this->assertNull($actualResult);
    }

    public function testGetValueWitValidOutput()
    {
        $temp = 70;
        $pin   = 3;

        $output = "Temp = $temp %";

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->willReturn($this->processBuilder);

        $this->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $process->expects($this->once())
            ->method('run');

        $process->expects($this->once())
            ->method('isSuccessful')
            ->willReturn(true);

        $process->expects($this->once())
            ->method('getOutput')
            ->willReturn($output);

        $actualResult = $this->subject->getValue($pin);

        $this->assertEquals($temp, $actualResult);
    }

    public function testIsSupportedExisting()
    {
        $output    = new DummyOutput();
        $parameter = 'parameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($parameter)
            ->willReturn(true);

        $actual = $this->subject->isSupported($parameter, $output);

        $this->assertTrue($actual);
    }

    public function testIsSupportedNotExisting()
    {
        $output    = new DummyOutput();
        $parameter = 'parameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($parameter)
            ->willReturn(false);

        $actual = $this->subject->isSupported($parameter, $output);

        $this->assertFalse($actual);
    }
}
