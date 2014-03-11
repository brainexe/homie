<?php

namespace Raspberry\Tests\Radio;

use Matze\Core\EventDispatcher\BackgroundEvent;
use Matze\Core\EventDispatcher\DelayedEvent;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Util\TimeParser;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\VO\RadioVO;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
		$this->_mock_time_parser = $this->getMock('Matze\Core\Util\TimeParser');
		$this->_mock_message_queue_gateway = $this->getMock('Matze\Core\MessageQueue\MessageQueueGateway');
		$this->_mock_dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');

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
		$background_event = new DelayedEvent($event, $timestamp);
		$this->_mock_dispatcher
			->expects($this->once())
			->method('dispatch')
			->with(DelayedEvent::DELAYED, $background_event);

		$this->_subject->addRadioJob($radio_vo, $time_string, $status);
	}

} 
