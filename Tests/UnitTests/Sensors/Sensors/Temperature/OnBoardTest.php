<?php

namespace Tests\Homie\Sensors\Sensors\Temperature;

use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Homie\Sensors\Sensors\Temperature\OnBoard;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;

/**
 * @covers Homie\Sensors\Sensors\Temperature\OnBoard
 */
class OnBoardTest extends TestCase
{

    /**
     * @var OnBoard
     */
    private $subject;

    /**
     * @var FileSystem|MockObject
     */
    private $fileSystem;

    /**
     * @var Glob|MockObject
     */
    private $glob;

    public function setUp()
    {
        $this->fileSystem = $this->getMock(FileSystem::class, [], [], '', false);
        $this->glob       = $this->getMock(Glob::class, [], [], '', false);

        $this->subject = new OnBoard($this->fileSystem, $this->glob);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(OnBoard::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $value     = 12201;
        $parameter = 'mockParameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($parameter)
            ->willReturn($value);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(12.2, $actual);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $sensor = new SensorVO();
        $sensor->parameter = $file;

        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage temperature.onboard: Thermal zone file does not exist: mockFile
     */
    public function testIsSupportedWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $sensor = new SensorVO();
        $sensor->parameter = $file;

        $this->subject->isSupported($sensor);
    }

    public function testGetDefinition()
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        $actual = $this->subject->getDefinition();

        $this->assertEquals($definition, $actual);
    }

    public function testSearch()
    {
        $result = ['search', 'foo/cooling'];

        $this->glob
            ->expects($this->once())
            ->method('execGlob')
            ->with('/sys/class/thermal/*/temp')
            ->willReturn($result);

        $actual = $this->subject->search();

        $this->assertEquals(['search'], $actual);
    }
}
