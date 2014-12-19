<?php

namespace Tests\Raspberry\Radio\RadioJobListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJobListener;
use Raspberry\Radio\RadioController;
use Raspberry\Radio\VO\RadioVO;

class RadioJobListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioJobListener
	 */
	private $_subject;

	/**
	 * @var RadioController|MockObject
	 */
	private $_mockRadioController;

	public function setUp() {
		$this->_mockRadioController = $this->getMock(RadioController::class, [], [], '', false);

		$this->_subject = new RadioJobListener($this->_mockRadioController);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleChangeEvent() {
		$radio_vo = new RadioVO();
		$radio_vo->code = $code = 'code';
		$radio_vo->pin = $pin = 'pin';

		$event = new RadioChangeEvent($radio_vo, RadioChangeEvent::CHANGE_RADIO);
		$event->status = $status = 'status';

		$this->_mockRadioController
			->expects($this->once())
			->method('setStatus')
			->with($code, $pin, $status);

		$this->_subject->handleChangeEvent($event);
	}

}
