<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;

use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureOnBoardSensor
 */
class TemperatureOnBoardSensorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TemperatureOnBoardSensor
     */
    private $subject;

    /**
     * @var FileSystem|MockObject
     */
    private $mockFileSystem;

    public function setUp()
    {
        $this->mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new TemperatureOnBoardSensor($this->mockFileSystem);
    }

    public function testGetSensorType()
    {
        $actual_result = $this->subject->getSensorType();

        $this->assertEquals(TemperatureOnBoardSensor::TYPE, $actual_result);
    }

    public function testGetValue()
    {
        $value = 12200;
        $pin   = 2;

        $this->mockFileSystem
        ->expects($this->once())
        ->method('fileGetContents')
        ->with(TemperatureOnBoardSensor::PATH)
        ->will($this->returnValue($value));

        $actual_result = $this->subject->getValue($pin);

        $this->assertEquals(12.2, $actual_result);
    }

    public function testIsSupported()
    {
        $file = TemperatureOnBoardSensor::PATH;

        $this->mockFileSystem
        ->expects($this->once())
        ->method('exists')
        ->with($file)
        ->will($this->returnValue(true));

        $output = new DummyOutput();
        $actual_result = $this->subject->isSupported($output);

        $this->assertTrue($actual_result);
    }

    public function testIsSupportedWhenNotSupported()
    {
        $file = TemperatureOnBoardSensor::PATH;

        $this->mockFileSystem
        ->expects($this->once())
        ->method('exists')
        ->with($file)
        ->will($this->returnValue(false));

        $output = new DummyOutput();
        $actual_result = $this->subject->isSupported($output);

        $this->assertFalse($actual_result);
    }
}
