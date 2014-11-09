<?php

namespace Tests\Raspberry\Webcam\WebcamListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Webcam\WebcamEvent;
use Raspberry\Webcam\WebcamListener;
use Raspberry\Webcam\Webcam;

/**
 * @Covers Raspberry\Webcam\WebcamListener
 */
class WebcamListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var WebcamListener
	 */
	private $_subject;

	/**
	 * @var Webcam|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockWebcam;

	public function setUp() {
		parent::setUp();

		$this->_mockWebcam = $this->getMock(Webcam::class, [], [], '', false);

		$this->_subject = new WebcamListener($this->_mockWebcam);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleWebcamEvent() {
		$name = 'shoot 123';
		$event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

		$this->_mockWebcam
			->expects($this->once())
			->method('takePhoto')
			->with($name);

		$this->_subject->handleWebcamEvent($event);
	}

}
