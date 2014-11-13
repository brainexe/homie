<?php

namespace Tests\Raspberry\Controller\EggTimerController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\EggTimerController;
use Raspberry\EggTimer\EggTimer;
use Symfony\Component\HttpFoundation\JsonResponse;
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
	 * @var EggTimer|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEggTimer;

	public function setUp() {
		parent::setUp();

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

		$expected = new JsonResponse([
			'jobs' => $jobs
		]);

		$this->assertEquals($expected, $actual_result);
	}

	public function testAdd() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->add($request);
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

		$expected = new JsonResponse($jobs);

		$this->assertEquals($expected, $actual_result);
	}

}
