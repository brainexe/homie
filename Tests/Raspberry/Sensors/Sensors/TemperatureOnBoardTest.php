<?php

namespace Tests\Raspberry\Sensors\Sensors;

use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\TemperatureOnBoard;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureOnBoard
 */
class TemperatureOnBoardTest extends TestCase
{

    /**
     * @var TemperatureOnBoard
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
        $this->fileSystem = $this->getMock(Filesystem::class, [], [], '', false);
        $this->glob     = $this->getMock(Glob::class, [], [], '', false);

        $this->subject = new TemperatureOnBoard($this->fileSystem, $this->glob);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(TemperatureOnBoard::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $value     = 12200;
        $parameter = 'mockParameter';

        $this->fileSystem
            ->expects($this->once())
            ->method('fileGetContents')
            ->with($parameter)
            ->willReturn($value);

        $actualResult = $this->subject->getValue($parameter);

        $this->assertEquals(12.2, $actualResult);
    }

    public function testIsSupported()
    {
        $file = 'mockFile';

        $this->fileSystem
            ->expects($this->once())
            ->method('exists')
            ->with($file)
            ->willReturn(true);

        $output       = new DummyOutput();
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

        $output       = new DummyOutput();
        $actualResult = $this->subject->isSupported($file, $output);

        $this->assertFalse($actualResult);
    }
}
