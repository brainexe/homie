<?php

namespace Tests\Homie\IFTTT;

use Homie\IFTTT\Trigger;
use phpmock\phpunit\PHPMock;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class TriggerTest extends TestCase
{
    use PHPMock;

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
        $this->subject = new Trigger($this->key);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';

        $function = $this->getFunctionMock('Homie\IFTTT', "file_get_contents");
        $function
            ->expects($this->once())
            ->with('https://maker.ifttt.com/trigger/my-test/with/key/myKey');

        $this->subject->trigger($eventName);
    }
}
