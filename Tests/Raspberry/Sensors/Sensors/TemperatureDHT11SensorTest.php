<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class TemperatureDHT11SensorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TemperatureDHT11Sensor
     */
    private $subject;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $mockProcessBuilder;

    /**
     * @var Filesystem|MockObject
     */
    private $mockFileSystem;

    public function setUp()
    {
        $this->mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new TemperatureDHT11Sensor($this->mockProcessBuilder, $this->mockFileSystem);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureDHT11Sensor::TYPE, $actualResult);
    }
    public function testGetValueWitInvalidOutput()
    {
        $pin = 3;

        $process = $this->getMock(Process::class, [], [], '', false);

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
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

        $this->mockProcessBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->willReturn($this->mockProcessBuilder);

        $this->mockProcessBuilder
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
}
