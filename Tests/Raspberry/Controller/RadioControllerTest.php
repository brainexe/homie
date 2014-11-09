<?php

namespace Tests\Raspberry\Controller\RadioController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\RadioController;
use Raspberry\Radio\Radios;
use Raspberry\Radio\RadioJob;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\RadioController
 */
class RadioControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioController
	 */
	private $_subject;

	/**
	 * @var Radios|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRadios;

	/**
	 * @var RadioJob|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRadioJob;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockRadios = $this->getMock(Radios::class, [], [], '', false);
		$this->_mockRadioJob = $this->getMock(RadioJob::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new RadioController($this->_mockRadios, $this->_mockRadioJob);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->index();
	}

	public function testSetStatus() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->setStatus($request, $radio_id, $status);
	}

	public function testAddRadio() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addRadio($request);
	}

	public function testDeleteRadio() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteRadio($request, $radio_id);
	}

	public function testEditRadio() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->editRadio($request);
	}

	public function testAddRadioJob() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addRadioJob($request);
	}

	public function testDeleteRadioJob() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteRadioJob($request, $job_id);
	}

}
