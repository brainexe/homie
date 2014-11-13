<?php

namespace Tests\BrainExe\MessageQueue\MessageQueueGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Redis\RedisScripts;
use BrainExe\Core\Util\Time;
use Redis;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers BrainExe\MessageQueue\MessageQueueGateway
 */
class MessageQueueGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var MessageQueueGateway
	 */
	private $_subject;

	/**
	 * @var RedisScripts|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedisScripts;

	/**
	 * @var Time|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTime;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;

	public function setUp() {

		$this->_mockRedisScripts = $this->getMock(RedisScripts::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->_subject = new MessageQueueGateway($this->_mockRedisScripts);
		$this->_subject->setTime($this->_mockTime);
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testFetchPendingEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->fetchPendingEvent();
	}

	public function testDeleteEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->deleteEvent($event_id, $event_type);
	}

	public function testAddEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addEvent($event, $timestamp);
	}

	public function testGetEventsByType() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getEventsByType($event_type, $since);
	}

	public function testRestoreEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->restoreEvent($event_id);
	}

	public function testCountJobs() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->countJobs();
	}

	public function testGetRedisScripts() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getRedisScripts();
	}

}
