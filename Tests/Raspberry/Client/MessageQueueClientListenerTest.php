<?php

namespace Tests\Raspberry\Client\MessageQueueClientListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Client\ExecuteCommandEvent;
use Raspberry\Client\MessageQueueClient;
use Raspberry\Client\MessageQueueClientListener;
use Raspberry\Client\LocalClient;
use Redis;

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
		$this->_mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new MessageQueueClientListener($this->_mockLocalClient);
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleExecuteEventWithoutReturn() {
		$command = 'command';

		$event = new ExecuteCommandEvent($command, true);

		$output = 'output';

		$this->_mockRedis
			->expects($this->once())
			->method('lPush')
			->with(MessageQueueClient::RETURN_CHANNEL, $output);

		$this->_mockLocalClient
			->expects($this->once())
			->method('executeWithReturn')
			->with($command)
			->will($this->returnValue($output));

		$this->_subject->handleExecuteEvent($event);
	}

}
