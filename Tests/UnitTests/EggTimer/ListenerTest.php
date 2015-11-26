<?php

namespace Tests\Homie\EggTimer\EggTimerListener;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\EggTimer\EggTimer;
use Homie\EggTimer\EggTimerEvent;
use Homie\EggTimer\Listener;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\Media\Sound;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @covers Homie\EggTimer\Listener
 */
class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Sound|MockObject
     */
    private $sound;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->sound      = $this->getMock(Sound::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Listener($this->sound);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testHandleEggTimerEventWithoutEspeak()
    {
        $event = new EggTimerEvent();

        $this->sound
            ->expects($this->once())
            ->method('playSound')
            ->with(ROOT . EggTimer::EGG_TIMER_RING_SOUND);

        $this->subject->handleEggTimerEvent($event);
    }

    public function testHandleEggTimerEventWithEspeak()
    {
        $text = 'text';
        $espeak = new EspeakVO($text);
        $event = new EggTimerEvent($espeak);

        $espeakEvent = new EspeakEvent($espeak);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($espeakEvent);

        $this->sound
            ->expects($this->once())
            ->method('playSound')
            ->with(ROOT . EggTimer::EGG_TIMER_RING_SOUND);

        $this->subject->handleEggTimerEvent($event);
    }
}
