<?php

namespace Tests\Raspberry\Sensors\Sensors;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\AbstractDHT11;
use Raspberry\Sensors\Sensors\HumidDHT11;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @covers Raspberry\Sensors\Sensors\HumidDHT11
 */
class HumidDHT11Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var HumidDHT11
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
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new HumidDHT11($this->processBuilder, $this->fileSystem, '/ada/');
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(HumidDHT11::TYPE, $actualResult);
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
        $humid = 70;
        $pin   = 3;

        $output = "Hum = $humid %";

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

        $this->assertEquals($humid, $actualResult);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertTrue($actualResult);
    }

    public function testIsSupportedWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertFalse($actualResult);
    }

}
