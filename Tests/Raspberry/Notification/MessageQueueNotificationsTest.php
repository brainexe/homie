<?php

namespace Tests\Raspberry\Notification\MessageQueueNotifications;

use BrainExe\Core\Redis\Redis;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Notification\MessageQueueNotifications;
use BrainExe\MessageQueue\MessageQueueGateway;

/**
 * @Covers Raspberry\Notification\MessageQueueNotifications
 */
class MessageQueueNotificationsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MessageQueueNotifications
	 */
	private $_subject;

	/**
	 * @var MessageQueueGateway|MockObject
	 */
	private $_mockMessageQueueGateway;

	/**
	 * @var Redis|MockObject
	 */
	private $_mockRedis;

	public function setUp() {
		$this->_mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new MessageQueueNotifications();
		$this->_subject->setMessageQueueGateway($this->_mockMessageQueueGateway);
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetNotification() {
		$count = 10;

		$this->_mockMessageQueueGateway
			->expects($this->once())
			->method('countJobs')
			->will($this->returnValue($count));

		$actual_result = $this->_subject->getNotification();

		$this->assertEquals($count, $actual_result);
	}

}
