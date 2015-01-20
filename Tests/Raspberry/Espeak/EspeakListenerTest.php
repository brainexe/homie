<?php

namespace Tests\Raspberry\Espeak\EspeakListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakListener;
use Raspberry\Espeak\Espeak;
use Raspberry\Espeak\EspeakVO;

/**
 * @Covers Raspberry\Espeak\EspeakListener
 */
class EspeakListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EspeakListener
     */
    private $subject;

    /**
     * @var Espeak|MockObject
     */
    private $mockEspeak;

    public function setUp()
    {
        $this->mockEspeak = $this->getMock(Espeak::class, [], [], '', false);

        $this->subject = new EspeakListener($this->mockEspeak);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleEspeakEvent()
    {
        $espeak_vo = new EspeakVO("text");
        $event = new EspeakEvent($espeak_vo);

        $this->mockEspeak
            ->expects($this->once())
            ->method('speak')
            ->with($espeak_vo->text, $espeak_vo->volume, $espeak_vo->speed, $espeak_vo->speaker);

        $this->subject->handleEspeakEvent($event);
    }
}
