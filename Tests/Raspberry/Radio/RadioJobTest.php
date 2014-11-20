<?php

namespace Raspberry\Tests\Radio;


use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Util\TimeParser;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\VO\RadioVO;

class RadioJobTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var RadioJob
	 */
	private $_subject;

	/**
	 * @var TimeParser|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_time_parser;

	/**
	 * @var MessageQueueGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_message_queue_gateway;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_dispatcher;

	public function setUp() {
		$this->_mock_time_parser = $this->getMock(TimeParser::class);
		$this->_mock_message_queue_gateway = $this->getMock(MessageQueueGateway::class, [], [], '', false);
		$this->_mock_dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new RadioJob($this->_mock_message_queue_gateway, $this->_mock_time_parser);
		$this->_subject->setEventDispatcher($this->_mock_dispatcher);
	}

	public function testGetJobs() {
		$jobs = [];

		$this->_mock_message_queue_gateway
			->expects($this->once())
			->method('getEventsByType')
			->with(RadioChangeEvent::CHANGE_RADIO)
			->will($this->returnValue($jobs));

		$actual_result = $this->_subject->getJobs();

		$this->assertEquals($jobs, $actual_result);
	}

	public function testAddJob() {
		$time_string = '1h';
		$timestamp = 1345465;
		$status = true;

		$radio_vo = new RadioVO();
		$radio_vo->id = 1;

		$this->_mock_time_parser
			->expects($this->once())
			->method('parseString')
			->with($time_string)
			->will($this->returnValue($timestamp));

		$event = new RadioChangeEvent($radio_vo, $status);
		$this->_mock_dispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event, $timestamp);

		$this->_subject->addRadioJob($radio_vo, $time_string, $status);
	}

	public function testDeleteJob() {
		$job_id = 19;

		$this->_mock_message_queue_gateway
			->expects($this->once())
			->method('deleteEvent')
			->with($job_id, RadioChangeEvent::CHANGE_RADIO);

		$this->_subject->deleteJob($job_id);

	}

} 
