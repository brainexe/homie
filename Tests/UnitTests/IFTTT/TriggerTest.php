<?php

namespace Tests\Homie\IFTTT;

use Homie\IFTTT\Trigger;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class TriggerTest extends TestCase
{

    /**
     * @var Trigger|MockObject
     */
    private $subject;

    /**
     * @var string
     */
    private $key = 'myKey';

    public function setUp()
    {
        $this->subject = $this->getMock(Trigger::class, ['makeRequest'], [$this->key]);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';

        $this->subject
            ->expects($this->once())
            ->method('makeRequest')
            ->with('https://maker.ifttt.com/trigger/my-test/with/key/myKey');

        $this->subject->trigger($eventName);
    }
}
