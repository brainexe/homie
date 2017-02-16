<?php

namespace Tests\Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;
use Homie\Display\Devices\Arduino;
use Homie\Node;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Display\Devices\Arduino
 */
class ArduinoTest extends TestCase
{

    /**
     * @var Arduino
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Arduino($this->dispatcher);
    }

    public function testGetType()
    {
        $this->assertEquals(Arduino::TYPE, Arduino::getType());
    }

    public function testOutput()
    {
        $string = "foo";
        $pin    = 10;

        $event = new SerialEvent(SerialEvent::LCD, $pin, $string);

        $node = new Node(1, Node::TYPE_SERVER);
        $node->setOptions(['displayPin' => $pin]);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->display($node, $string);
    }
}
