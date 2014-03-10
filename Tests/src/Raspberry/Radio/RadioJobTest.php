<?php

namespace Raspberry\Tests\Radio;

use Matze\Core\Util\TimeParser;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\RadioJobGateway;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;

class RadioJobTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var RadioJob
	 */
	private $_subject;

	/**
	 * @var RadioJobGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_radio_job_gateway;

	/**
	 * @var TimeParser|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_time_parser;

	public function setUp() {
		$this->_mock_radio_job_gateway = $this->getMock('Raspberry\Radio\RadioJobGateway');
		$this->_mock_time_parser = $this->getMock('Matze\Core\Util\TimeParser');

		$mock_dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');

		$this->_subject = new RadioJob($this->_mock_radio_job_gateway, $this->_mock_time_parser);
		$this->_subject->setEventDispatcher($mock_dispatcher);
	}

	public function testGetJobs() {
		$jobs = [];

		$this->_mock_radio_job_gateway
			->expects($this->once())
			->method('getJobs')
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

		$this->_mock_radio_job_gateway
			->expects($this->once())
			->method('addRadioJob')
			->with($radio_vo->id, $timestamp, $status);

		$this->_subject->addRadioJob($radio_vo, $time_string, $status);
	}

} 
