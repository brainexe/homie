<?php

namespace Tests\Homie\Arduino;

use BrainExe\Core\Util\Glob;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Arduino\Serial;
use Homie\Arduino\SerialEvent;
use Homie\Client\ClientInterface;
use RuntimeException;

/**
 * @covers Homie\Arduino\Serial
 */
class SerialTest extends TestCase
{
    const FILE = '/test.txt';

    /**
     * @var Serial
     */
    private $subject;

    /**
     * @var Glob|MockObject
     */
    private $glob;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->glob  = $this->getMock(Glob::class, [], [], '', false);
        $this->client  = $this->getMock(ClientInterface::class, [], [], '', false);
        $this->subject = new Serial($this->glob, $this->client, '/dev/ttyACM*', 57600);
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
     * @expectedExceptionMessage No file found matching /dev/ttyACM*
     */
    public function testSendSerialNotFound()
    {
        $action = 'a';
        $pin    = 12;
        $value  = 2;

        $this->glob
            ->expects($this->once())
            ->method('execGlob')
            ->with('/dev/ttyACM*')
            ->willReturn([]);

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

        $this->glob
            ->expects($this->once())
            ->method('execGlob')
            ->with('/dev/ttyACM*')
            ->willReturn([$file]);

        $this->client
            ->expects($this->once())
            ->method('execute')
            ->with("sudo stty -F '$file' 57600");

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
