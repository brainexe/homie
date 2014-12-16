<?php

namespace Tests\Raspberry\Espeak;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Espeak\InputControl;

/**
 * @Covers Raspberry\Espeak\InputControl
 */
class InputControlTest extends TestCase {

	/**
	 * @var InputControl
	 */
	private $subject;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $mockEventDispatcher;

	public function setUp() {
		$this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->subject = new InputControl();
		$this->subject->setEventDispatcher($this->mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$actualResult = $this->subject->getSubscribedEvents();

		$this->assertInternalType('array', $actualResult);
	}

	public function testSay() {
		$input_event = new Event();
		$input_event->match = 'text';

		$event = new EspeakEvent(new EspeakVO('text'));

		$this->mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$this->subject->say($input_event);
	}

}
