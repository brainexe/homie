<?php

namespace Tests\Homie\Remote\Event;

use Homie\Remote\Event\ReceivedEvent;
use PHPUnit\Framework\TestCase;

class ReceivedEventTest extends TestCase
{
    public function testEvent()
    {
        $code  = 'myCode';
        $event = new ReceivedEvent($code);

        $this->assertEquals($code, $event->getCode());
    }
}
