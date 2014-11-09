<?php

namespace Tests\Raspberry\Radio\RadioJobListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioJobListener;
use Raspberry\Radio\RadioController;

/**
 * @Covers Raspberry\Radio\RadioJobListener
 */
class RadioJobListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioJobListener
	 */
	private $_subject;

	/**
	 * @var RadioController|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRadioController;


	public function setUp() {
		parent::setUp();

		$this->_mockRadioController = $this->getMock(RadioController::class, [], [], '', false);

		$this->_subject = new RadioJobListener($this->_mockRadioController);

	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleChangeEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->handleChangeEvent($event);
	}

}
