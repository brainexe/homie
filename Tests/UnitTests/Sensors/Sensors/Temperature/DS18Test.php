<?php

namespace Tests\Homie\Sensors\Sensors\Temperature;

use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Sensors\Temperature\DS18;
use Homie\Sensors\SensorVO;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;

/**
 * @covers \Homie\Sensors\Sensors\Temperature\DS18
 */
class DS18Test extends TestCase
{

    /**
     * @var DS18
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
        $this->fileSystem = $this->createMock(FileSystem::class);
        $this->glob       = $this->createMock(Glob::class);

        $this->subject = new DS18($this->fileSystem, $this->glob);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(DS18::TYPE, $actualResult);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid file: mockFile
     */
    public function testGetValueWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $sensor = new SensorVO();
        $sensor->parameter = $file;

        $this->subject->getValue($sensor);
    }

    /**
     * @param string $content
     * @param float|null $expected
     * @dataProvider provideContent
     */
    public function testGetValue($content, $expected)
    {
        $file = "mockFile";

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $this->fileSystem
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($file)
            ->willReturn($content);

        $sensor = new SensorVO();
        $sensor->parameter = $file;

        if (null === $expected) {
            $this->expectException(InvalidSensorValueException::class);
        }
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals($expected, $actual);
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
        $expected = ['search'];

        $this->glob
            ->expects($this->once())
            ->method('execGlob')
            ->with('/sys/bus/w1/devices/*/w1_slave')
            ->willReturn($expected);

        $actual = $this->subject->search();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array[]
     */
    public function provideContent()
    {
        return [
            ['', null],
            ['invalid', null],
            ['YES foo', null],
            ['YES t=0', null],
            ['YES t=85000', null],
            ['YES t=-7000', -7],
            ['YES t=70000', 70],
            ['YES t=70001', 70.0],
            ['YES t=700010', null],
            ['YES t=-70001', null],
        ];
    }
}
