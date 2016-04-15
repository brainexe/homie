<?php

namespace Tests\Homie\Remote\Event;

use Homie\Remote\Event\SendEvent;
use PHPUnit_Framework_TestCase as TestCase;

class SendEventTest extends TestCase
{
    public function testEvent()
    {
        $code  = 'myCode';
        $event = new SendEvent($code);
        
        $this->assertEquals($code, $event->getCode());
    }
}
