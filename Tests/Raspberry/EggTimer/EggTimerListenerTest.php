<?php

namespace Tests\Raspberry\EggTimer\EggTimerListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\EggTimer\EggTimer;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\EggTimer\EggTimerListener;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Media\Sound;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\EggTimer\EggTimerListener
 */
class EggTimerListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var EggTimerListener
     */
    private $subject;

    /**
     * @var Sound|MockObject
     */
    private $mockSound;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockSound = $this->getMock(Sound::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new EggTimerListener($this->mockSound);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleEggTimerEventWithoutEspeak()
    {
        $event = new EggTimerEvent();

        $this->mockSound
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

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($espeakEvent);

        $this->mockSound
            ->expects($this->once())
            ->method('playSound')
            ->with(ROOT . EggTimer::EGG_TIMER_RING_SOUND);

        $this->subject->handleEggTimerEvent($event);
    }
}
