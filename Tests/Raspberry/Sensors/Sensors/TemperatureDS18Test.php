<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureDS18;

use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\TemperatureDS18;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureDS18
 */
class TemperatureDS18Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var TemperatureDS18
     */
    private $subject;

    /**
     * @var FileSystem|MockObject
     */
    private $mockFileSystem;

    public function setUp()
    {
        $this->mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

        $this->subject = new TemperatureDS18($this->mockFileSystem);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureDS18::TYPE, $actualResult);
    }

    public function testGetValueWhenNotSupported()
    {
        $pin = 12;

        $file = sprintf(TemperatureDS18::PIN_FILE, $pin);

        $this->mockFileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $actualResult = $this->subject->getValue($pin);

        $this->assertNull($actualResult);
    }

    /**
     * @param string $content
     * @param string|null $expectedResult
     * @dataProvider provideContent
     */
    public function testGetValue($content, $expectedResult)
    {
        $pin = 12;

        $file = sprintf(TemperatureDS18::PIN_FILE, $pin);

        $this->mockFileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $this->mockFileSystem
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($file)
            ->willReturn($content);

        $actualResult = $this->subject->getValue($pin);

        $this->assertEquals($expectedResult, $actualResult);
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
        $file = sprintf(TemperatureDS18::BUS_DIR);

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
        $file = sprintf(TemperatureDS18::BUS_DIR);

        $this->mockFileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $output = new DummyOutput();
        $actualResult = $this->subject->isSupported($output);

        $this->assertFalse($actualResult);
    }

    /**
     * @return array[]
     */
    public function provideFormatValues()
    {
        return [
        [100, '100.00°'],
        [0, '0.00°'],
        ['', '0.00°'],
        [10.2222, '10.22°'],
        [-10.2222, '-10.22°'],
        ];
    }

    /**
     * @return array[]
     */
    public function provideEspeakText()
    {
        return [
        [100, '100,0 Degree'],
        [0, '0,0 Degree'],
        ['', '0,0 Degree'],
        [10.2222, '10,2 Degree'],
        [-10.2222, '-10,2 Degree'],
        ];
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
        ['YES t=70000', 70]
        ,
        ['YES t=70001', 70.001],
        ];
    }
}
