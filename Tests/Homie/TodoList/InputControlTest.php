<?php

namespace Tests\Homie\TodoList;

use Homie\TodoList\InputControl;
use Homie\TodoList\TodoReminder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\TodoList\InputControl
 */
class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;

    /**
     * @var TodoReminder|MockObject
     */
    private $reminder;

    public function setUp()
    {
        $this->reminder = $this->getMock(TodoReminder::class, [], [], '', false);
        $this->subject  = new InputControl($this->reminder);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actual);
    }

    public function testNotify()
    {
        $this->reminder
            ->expects($this->once())
            ->method('sendNotification');

        $this->subject->notify();
    }
}
