<?php

namespace Tests\Raspberry\Arduino;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Arduino\Serial;
use Raspberry\Arduino\SerialEvent;
use Symfony\Component\Finder\Finder;

/**
 * @Covers Raspberry\Arduino\Serial
 */
class SerialTest extends TestCase
{

    /**
     * @var Serial
     */
    private $subject;

    /**
     * @var Finder|MockObject
     */
    private $mockFinder;

    public function setUp()
    {
        $this->mockFinder = $this->getMock(Finder::class, [], [], '', false);
        $this->subject    = new Serial($this->mockFinder, 'ttyACM*', 57600);
    }

    public function testSendSerial()
    {
        $action = 'a';
        $pin    = 12;
        $value  = 2;

        $event = new SerialEvent($action, $pin, $value);

        $this->markTestIncomplete('TODO');

        $this->subject->sendSerial($event);
    }

}
