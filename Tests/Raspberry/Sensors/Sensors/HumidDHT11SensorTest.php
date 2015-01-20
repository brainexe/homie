<?php

namespace Tests\Raspberry\Sensors\Sensors\HumidDHT11Sensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\AbstractDHT11Sensor;
use Raspberry\Sensors\Sensors\HumidDHT11Sensor;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers Raspberry\Sensors\Sensors\HumidDHT11Sensor
 */
class HumidDHT11SensorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var HumidDHT11Sensor
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

        $this->subject = new HumidDHT11Sensor($this->mockProcessBuilder, $this->mockFileSystem);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(HumidDHT11Sensor::TYPE, $actualResult);
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
        $humid = 70;
        $pin   = 3;

        $output = "Hum = $humid %";

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

        $this->assertEquals($humid, $actualResult);
    }

    /**
     * @param float $given
     * @param string $expectedResult
     * @dataProvider provideFormatValues
     */
    public function testFormatValue($given, $expectedResult)
    {
        $actualResult = $this->subject->formatValue($given);

        $this->assertEquals($expectedResult, $actualResult);
    }
    /**
     * @param float $given
     * @param string $expectedResult
     * @dataProvider provideEspeakText
     */
    public function testGetEspeakText($given, $expectedResult)
    {
        $actualResult = $this->subject->getEspeakText($given);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIsSupported()
    {
        $file = sprintf(AbstractDHT11Sensor::ADA_SCRIPT);

        $this->mockFileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($output);

        $this->assertTrue($actualResult);
    }

    public function testIsSupportedWhenNotSupported()
    {
        $file = sprintf(AbstractDHT11Sensor::ADA_SCRIPT);

        $this->mockFileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($output);

        $this->assertFalse($actualResult);
    }

    public function provideEspeakText()
    {
        return [
        [100, '100 Percent'],
        [0, '0 Percent'],
        ['', '0 Percent'],
        [10.2222, '10 Percent'],
        [-10.2222, '-10 Percent'],
        ];
    }

    public function provideFormatValues()
    {
        return [
        [100, '100%'],
        [0, '0%'],
        ['', '0%'],
        [10.2222, '10%'],
        [-10.2222, '-10%'],
        ];
    }
}
