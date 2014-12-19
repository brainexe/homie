<?php

namespace Tests\Raspberry\Controller\EspeakController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Raspberry\Controller\EspeakController;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

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
	 * @var Espeak|MockObject
	 */
	private $_mockEspeak;

	/**
	 * @var TimeParser|MockObject
	 */
	private $_mockTimeParser;

	/**
	 * @var EventDispatcher|MockObject
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

		$actual_result = $this->_subject->index();

		$expected_result = [
			'speakers' => $speakers,
			'jobs' => $jobs
		];

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testSpeak() {
		$request = new Request();

		$speaker = 'speaker';
        $text = 'text';
        $volume = 120;
        $speed = 80;
        $delay_raw = 'delay_row';
		$timestamp = 10;

		$request->request->set('speaker', $speaker);
        $request->request->set('text', $text);
        $request->request->set('volume', $volume);
        $request->request->set('speed', $speed);
        $request->request->set('delay', $delay_raw);

		$this->_mockTimeParser
			->expects($this->once())
			->method('parseString')
			->with($delay_raw)
			->will($this->returnValue($timestamp));

        $espeak_vo = new EspeakVO($text, $volume, $speed, $speaker);
        $event = new EspeakEvent($espeak_vo);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event, $timestamp);

		$pending_jobs = ['pending_jobs'];

		$this->_mockEspeak
			->expects($this->once())
			->method('getPendingJobs')
			->will($this->returnValue($pending_jobs));

		$actual_result = $this->_subject->speak($request);

		$this->assertEquals($pending_jobs, $actual_result);
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

		$this->assertTrue($actual_result);
	}

}
