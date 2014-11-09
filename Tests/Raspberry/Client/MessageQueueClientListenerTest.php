<?php

namespace Tests\Raspberry\Client\MessageQueueClientListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Client\MessageQueueClientListener;
use Raspberry\Client\LocalClient;
use Redis;

/**
 * @Covers Raspberry\Client\MessageQueueClientListener
 */
class MessageQueueClientListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MessageQueueClientListener
	 */
	private $_subject;

	/**
	 * @var LocalClient|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLocalClient;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;


	public function setUp() {
		parent::setUp();

		$this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new MessageQueueClientListener($this->_mockLocalClient);
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetSubscribedEvents() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->getSubscribedEvents();
	}

	public function testHandleExecuteEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->handleExecuteEvent($event);
	}

}
