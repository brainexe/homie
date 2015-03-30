<?php

namespace Tests\Raspberry\Arduino;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Arduino\Serial;
use Raspberry\Arduino\SerialEvent;
use Raspberry\Client\ClientInterface;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\Iterator;

/**
 * @covers Raspberry\Arduino\Serial
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
     * @var ClientInterface|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->finder  = $this->getMock(Finder::class, [], [], '', false);
        $this->client  = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->subject = new Serial($this->finder, $this->client, 'ttyACM*', 57600);
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

    /**
     * @dataProvider provideActions
     * @param string $action
     * @param int $pin
     * @param int $value
     * @param string $expectedResult
     */
    public function testSendSerial($action, $pin, $value, $expectedResult)
    {

        $file = __DIR__ . self::FILE;

        $result = new Iterator();
        $result->attach(new SplFileInfo($file, $file, $file));

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

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with("sudo stty -F $file 57600");

        $event = new SerialEvent($action, $pin, $value);

        $this->subject->sendSerial($event);

        $actualResult = file_get_contents($file);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideActions()
    {
        return [
            ['a', 12, 1, "a:12:1\n"],
            ['a', 100000, -121, "a:100000:-121\n"],
            ['s', 0, 0, "s:0:0\n"],
            ['s', null, false, "s:0:0\n"],
        ];
    }
}
