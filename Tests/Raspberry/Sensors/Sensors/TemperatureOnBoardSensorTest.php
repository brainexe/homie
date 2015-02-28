<?php

namespace Tests\Raspberry\Sensors\Sensors;

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
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureOnBoardSensor::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $value = 12200;
        $pin   = null;

        $this->mockFileSystem
            ->expects($this->once())
            ->method('fileGetContents')
            ->with(TemperatureOnBoardSensor::PATH)
            ->willReturn($value);

        $actualResult = $this->subject->getValue($pin);

        $this->assertEquals(12.2, $actualResult);
    }

    public function testIsSupported()
    {
        $file = TemperatureOnBoardSensor::PATH;

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
        $file = TemperatureOnBoardSensor::PATH;

        $this->mockFileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($output);

        $this->assertFalse($actualResult);
    }
}
