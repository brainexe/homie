<?php

namespace Tests\Homie\Espeak;

use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use PHPUnit_Framework_TestCase as TestCase;

class EspeakEventTest extends TestCase
{
    public function testConstruct()
    {
        $espeak = new EspeakVO('foo');
        $event  = new EspeakEvent($espeak);

        $this->assertEquals(EspeakEvent::SPEAK, $event->event_name);
        $this->assertEquals($espeak, $event->espeak);
    }
}
