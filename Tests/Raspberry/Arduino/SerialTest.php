<?php

namespace Tests\Raspberry\Arduino;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Arduino\Serial;
use Raspberry\Arduino\SerialEvent;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\Iterator;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers Raspberry\Arduino\Serial
 */
class SerialTest extends TestCase
{
    const FILE = '/test.txt';

    /**
     * @var Serial
     */
    private $subject;

    /**
     * @var Finder|MockObject
     */
    private $finder;

    /**
     * @var ProcessBuilder|MockObject
     */
    private $processBuilder;

    public function setUp()
    {
        $this->finder         = $this->getMock(Finder::class, [], [], '', false);
        $this->processBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
        $this->subject = new Serial($this->finder, $this->processBuilder, 'ttyACM*', 57600);
    }

    public function tearDown()
    {
        $file = __DIR__ . self::FILE;
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage No file found matching ttyACM*
     */
    public function testSendSerialNotFound()
    {
        $action = 'a';
        $pin    = 12;
        $value  = 2;

        $result = new Iterator();

        $this->finder
            ->expects($this->once())
            ->method('in')
            ->with('/dev')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('name')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($result);

        $event = new SerialEvent($action, $pin, $value);

        $this->subject->sendSerial($event);
    }

    public function testSendSerial()
    {
        $this->markTestIncomplete();
        
        $action = 'a';
        $pin    = 12;
        $value  = 2;

        $file = __DIR__ . self::FILE;

        $result = new Iterator();
        $result->attach(new SplFileInfo(self::FILE, $file, $file));

        $this->finder
            ->expects($this->once())
            ->method('in')
            ->with('/dev')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('name')
            ->willReturnSelf();
        $this->finder
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn($result);

        $event = new SerialEvent($action, $pin, $value);

        $this->subject->sendSerial($event);
    }
}
