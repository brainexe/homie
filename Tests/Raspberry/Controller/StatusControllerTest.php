<?php

namespace Tests\Raspberry\Controller\StatusController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\StatusController;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\StatusController
 */
class StatusControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var StatusController
	 */
	private $_subject;

	/**
	 * @var MessageQueueGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockMessageQueueGateway;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new StatusController($this->_mockMessageQueueGateway);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->index();
	}

	public function testDeleteJob() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteJob($request);
	}

	public function testStartSelfUpdate() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->startSelfUpdate();
	}

}
