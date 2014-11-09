<?php

namespace Tests\Raspberry\Espeak\EspeakListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakListener;
use Raspberry\Espeak\Espeak;
use Raspberry\Espeak\EspeakVO;

/**
 * @Covers Raspberry\Espeak\EspeakListener
 */
class EspeakListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EspeakListener
	 */
	private $_subject;

	/**
	 * @var Espeak|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEspeak;


	public function setUp() {
		parent::setUp();

		$this->_mockEspeak = $this->getMock(Espeak::class, [], [], '', false);

		$this->_subject = new EspeakListener($this->_mockEspeak);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleEspeakEvent() {
		$espeak_vo = new EspeakVO("text");
		$event = new EspeakEvent($espeak_vo);

		$this->_mockEspeak
			->expects($this->once())
			->method('speak')
			->with($espeak_vo->text, $espeak_vo->volume, $espeak_vo->speed, $espeak_vo->speaker);

		$this->_subject->handleEspeakEvent($event);
	}

}
