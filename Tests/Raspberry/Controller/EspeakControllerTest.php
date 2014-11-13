<?php

namespace Tests\Raspberry\Controller\EspeakController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\EspeakController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Espeak\Espeak;
use BrainExe\Core\Util\TimeParser;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\EspeakController
 */
class EspeakControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EspeakController
	 */
	private $_subject;

	/**
	 * @var Espeak|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEspeak;

	/**
	 * @var TimeParser|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTimeParser;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockEspeak = $this->getMock(Espeak::class, [], [], '', false);
		$this->_mockTimeParser = $this->getMock(TimeParser::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new EspeakController($this->_mockEspeak, $this->_mockTimeParser);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndex() {
		$speakers = ['speakers'];
		$jobs = ['jobs'];

		$this->_mockEspeak
			->expects($this->once())
			->method('getSpeakers')
			->will($this->returnValue($speakers));

		$this->_mockEspeak
			->expects($this->once())
			->method('getPendingJobs')
			->will($this->returnValue($jobs));

		$expected_result = new JsonResponse([
			'speakers' => $speakers,
			'jobs' => $jobs
		]);

		$actual_result = $this->_subject->index();
		$this->assertEquals($expected_result, $actual_result);
	}

	public function testSpeak() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->speak($request);
	}

	public function testDeleteJobJob() {
		$job_id = 10;
		$request = new Request();
		$request->request->set('job_id', $job_id);

		$this->_mockEspeak
			->expects($this->once())
			->method('deleteJob')
			->will($this->returnValue($job_id));


		$actual_result = $this->_subject->deleteJob($request);

		$expected_result = new JsonResponse(true);
		$this->assertEquals($expected_result, $actual_result);
	}

}
