<?php

namespace Tests\Raspberry\Controller\EggTimerController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\EggTimerController;
use Raspberry\EggTimer\EggTimer;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\Controller\EggTimerController
 */
class EggTimerControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EggTimerController
	 */
	private $_subject;

	/**
	 * @var EggTimer|MockObject
	 */
	private $_mockEggTimer;

	public function setUp() {
		$this->_mockEggTimer = $this->getMock(EggTimer::class, [], [], '', false);

		$this->_subject = new EggTimerController($this->_mockEggTimer);
	}

	public function testIndex() {
		$jobs = [];

		$this->_mockEggTimer
			->expects($this->once())
			->method('getJobs')
			->will($this->returnValue($jobs));

		$actual_result = $this->_subject->index();

		$expected = [
			'jobs' => $jobs
		];

		$this->assertEquals($expected, $actual_result);
	}

	public function testAdd() {
		$time = 'time';
		$text = 'text';

		$request = new Request();
		$request->request->set('text', $text);
		$request->request->set('time', $time);

		$this->_mockEggTimer
			->expects($this->once())
			->method('addNewJob')
			->with($time, $text);

		$jobs = ['jobs'];
		$this->_mockEggTimer
			->expects($this->once())
			->method('getJobs')
			->will($this->returnValue($jobs));

		$actual_result = $this->_subject->add($request);

		$this->assertEquals($jobs, $actual_result);
	}

	public function testDeleteEggTimer() {
		$request = new Request();
		$job_id = 10;

		$jobs = [];

		$this->_mockEggTimer
			->expects($this->once())
			->method('deleteJob')
			->with($job_id);

		$this->_mockEggTimer
			->expects($this->once())
			->method('getJobs')
			->will($this->returnValue($jobs));

		$actual_result = $this->_subject->deleteEggTimer($request, $job_id);

		$this->assertEquals($jobs, $actual_result);
	}

}
