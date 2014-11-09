<?php

namespace Tests\Raspberry\Espeak\EggTimerListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Espeak\EggTimerListener;
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
		parent::setUp();

		$this->_mockSound = $this->getMock(Sound::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new EggTimerListener($this->_mockSound);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleEggTimerEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->handleEggTimerEvent($event);
	}

}
