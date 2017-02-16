<?php

namespace Tests\Homie\Espeak;

use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use PHPUnit\Framework\TestCase;

class EspeakEventTest extends TestCase
{
    public function testConstruct()
    {
        $espeak = new EspeakVO('foo');
        $event  = new EspeakEvent($espeak);

        $this->assertEquals(EspeakEvent::SPEAK, $event->getEventName());
        $this->assertEquals($espeak, $event->getEspeak());
    }
}
