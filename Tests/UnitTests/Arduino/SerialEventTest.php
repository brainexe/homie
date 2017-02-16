<?php

namespace Tests\Homie\Arduino;

use PHPUnit\Framework\TestCase;
use Homie\Arduino\SerialEvent;

class SerialEventTest extends TestCase
{
    public function testAll()
    {
        $action = 'action';
        $pin    = 12;
        $value  = 1;

        $event = new SerialEvent($action, $pin, $value);

        $this->assertEquals($action, $event->getAction());
        $this->assertEquals($pin, $event->getPin());
        $this->assertEquals($value, $event->getValue());
    }
}
