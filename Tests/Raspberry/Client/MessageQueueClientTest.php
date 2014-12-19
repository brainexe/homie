<?php

namespace Tests\Raspberry\Client\MessageQueueClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Client\ExecuteCommandEvent;
use Raspberry\Client\MessageQueueClient;
use BrainExe\Core\Redis\Redis;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Client\MessageQueueClient
 */
class MessageQueueClientTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MessageQueueClient
	 */
	private $_subject;

	/**
	 * @var Redis|MockObject
	 */
	private $_mockRedis;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_subject = new MessageQueueClient();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testExecute() {
		$command = 'command';

		$event = new ExecuteCommandEvent($command, false);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event, 0);

		$this->_subject->execute($command);
	}

	public function testExecuteWithReturn() {
		$command = 'command';

		$event = new ExecuteCommandEvent($command, true);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event);

		$this->_mockRedis
			->expects($this->once())
			->method('brPop')
			->with(MessageQueueClient::RETURN_CHANNEL, 5);

		$this->_subject->executeWithReturn($command);
	}

}
