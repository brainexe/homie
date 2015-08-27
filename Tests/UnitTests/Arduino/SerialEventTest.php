<?php

namespace Tests\Homie\Arduino;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Arduino\SerialEvent;

class SerialEventTest extends TestCase
{
    public function testAll()
    {
        $action = 'action';
        $pin    = 'pin';
        $value  = 'value';

        $event = new SerialEvent($action, $pin, $value);

        $this->assertEquals($action, $event->getAction());
        $this->assertEquals($pin, $event->getPin());
        $this->assertEquals($value, $event->getValue());
    }
}
