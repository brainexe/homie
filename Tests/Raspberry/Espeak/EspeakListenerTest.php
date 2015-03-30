<?php

namespace Tests\Raspberry\Espeak;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakListener;
use Raspberry\Espeak\Espeak;
use Raspberry\Espeak\EspeakVO;

/**
 * @covers Raspberry\Espeak\EspeakListener
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
    private $espeak;

    public function setUp()
    {
        $this->espeak  = $this->getMock(Espeak::class, [], [], '', false);
        $this->subject = new EspeakListener($this->espeak);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleEspeakEvent()
    {
        $espeakVo = new EspeakVO("text");
        $event    = new EspeakEvent($espeakVo);

        $this->espeak
            ->expects($this->once())
            ->method('speak')
            ->with(
                $espeakVo->text,
                $espeakVo->volume,
                $espeakVo->speed,
                $espeakVo->speaker
            );

        $this->subject->handleEspeakEvent($event);
    }
}
