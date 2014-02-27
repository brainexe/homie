<?php

namespace Raspberry\Tests\Radio;

use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\RadioJobGateway;
use Raspberry\Radio\Radios;

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
	 * @var Radios|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_radios;

	public function setUp() {
		$this->_mock_radio_job_gateway = $this->getMock('Raspberry\Radio\RadioJobGateway');
		$this->_mock_radios = $this->getMock('Raspberry\Radio\Radios', [], [], '', false);

		$this->_subject = new RadioJob($this->_mock_radio_job_gateway, $this->_mock_radios);
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

} 
