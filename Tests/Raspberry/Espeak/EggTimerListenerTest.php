<?php

namespace Tests\Raspberry\Espeak\EggTimerListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\EggTimer\EggTimer;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\Espeak\EggTimerListener;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Media\Sound;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Espeak\EggTimerListener
 */
class EggTimerListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EggTimerListener
	 */
	private $_subject;

	/**
	 * @var Sound|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSound;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockSound = $this->getMock(Sound::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new EggTimerListener($this->_mockSound);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleEggTimerEventWithoutEspeak() {
		$event = new EggTimerEvent();

		$this->_mockSound
			->expects($this->once())
			->method('playSound')
			->with(ROOT . EggTimer::EGG_TIMER_RING_SOUND);

		$this->_subject->handleEggTimerEvent($event);
	}

	public function testHandleEggTimerEventWithEspeak() {
		$text = 'text';
		$espeak = new EspeakVO($text);
		$event = new EggTimerEvent($espeak);

		$espeak_event = new EspeakEvent($espeak);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($espeak_event);

		$this->_mockSound
			->expects($this->once())
			->method('playSound')
			->with(ROOT . EggTimer::EGG_TIMER_RING_SOUND);

		$this->_subject->handleEggTimerEvent($event);
	}

}
