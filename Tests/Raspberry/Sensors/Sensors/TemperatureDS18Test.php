<?php

namespace Tests\Raspberry\Sensors\Sensors;

use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\TemperatureDS18;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureDS18
 */
class TemperatureDS18Test extends TestCase
{

    /**
     * @var TemperatureDS18
     */
    private $subject;

    /**
     * @var FileSystem|MockObject
     */
    private $fileSystem;

    /**
     * @var Glob
     */
    private $glob;

    public function setUp()
    {
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);
        $this->glob       = $this->getMock(Glob::class, [], [], '', false);

        $this->subject = new TemperatureDS18($this->fileSystem, $this->glob);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureDS18::TYPE, $actualResult);
    }

    public function testGetValueWhenNotSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(false);

        $actualResult = $this->subject->getValue($file);

        $this->assertNull($actualResult);
    }

    /**
     * @param string $content
     * @param string|null $expectedResult
     * @dataProvider provideContent
     */
    public function testGetValue($content, $expectedResult)
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

        $actualResult = $this->subject->getValue($file);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIsSupported()
    {
        $file = "mockFile";

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

        $this->fileSystem->expects($this->once())->method('exists')->with($file)->willReturn(false);

        $output       = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertFalse($actualResult);
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
            ['YES t=70000', 70],
            ['YES t=70001', 70.001],
        ];
    }
}
