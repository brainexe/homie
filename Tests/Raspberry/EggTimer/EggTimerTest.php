<?php

namespace Tests\Raspberry\EggTimer\EggTimer;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\EggTimer\EggTimer;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\Util\Time;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\EggTimer\EggTimerEvent;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

class EggTimerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EggTimer
	 */
	private $_subject;

	/**
	 * @var MessageQueueGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockMessageQueueGateway;

	/**
	 * @var TimeParser|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTimeParser;

	/**
	 * @var Time|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTime;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockMessageQueueGateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
		$this->_mockTimeParser = $this->getMock(TimeParser::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_subject = new EggTimer($this->_mockMessageQueueGateway, $this->_mockTimeParser);
		$this->_subject->setTime($this->_mockTime);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testAddNewJobWithoutText() {
		$time = 'time';
		$text = '';

		$timestamp = 100;

		$espeak_vo = null;
        $event = new EggTimerEvent($espeak_vo);

		$this->_mockTimeParser
			->expects($this->once())
			->method('parseString')
			->with($time)
			->will($this->returnValue($timestamp));

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event, $timestamp);

		$this->_subject->addNewJob($time, $text);
	}

	public function testAddNewJobWithText() {
		$time = 'time';
		$text = 'text';

		$timestamp = 100;

		$espeak_vo = new EspeakVO($text);
        $event = new EggTimerEvent($espeak_vo);

		$this->_mockTimeParser
			->expects($this->once())
			->method('parseString')
			->with($time)
			->will($this->returnValue($timestamp));

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event, $timestamp);

		$this->_subject->addNewJob($time, $text);
	}

	public function testDeleteJob() {
		$job_id = 10;

		$this->_mockMessageQueueGateway
			->expects($this->once())
			->method('deleteEvent')
			->with($job_id, EggTimerEvent::DONE);

		$this->_subject->deleteJob($job_id);
	}

	public function testGetJobs() {
		$now = 1000;
		$jobs = [];

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$this->_mockMessageQueueGateway
			->expects($this->once())
			->method('getEventsByType')
			->with(EggTimerEvent::DONE, $now)
			->will($this->returnValue($jobs));

		$actual_result = $this->_subject->getJobs();

		$this->assertEquals($jobs, $actual_result);
	}

}
