<?php

namespace Tests\Raspberry\Controller\RadioController;

use BrainExe\Core\Controller\ControllerInterface;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Controller\RadioController;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\VO\RadioVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Radio\Radios;
use Raspberry\Radio\RadioJob;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\RadioController
 */
class RadioControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioController
	 */
	private $_subject;

	/**
	 * @var Radios|MockObject
	 */
	private $_mockRadios;

	/**
	 * @var RadioJob|MockObject
	 */
	private $_mockRadioJob;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockRadios = $this->getMock(Radios::class, [], [], '', false);
		$this->_mockRadioJob = $this->getMock(RadioJob::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new RadioController($this->_mockRadios, $this->_mockRadioJob);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndex() {
		$radios_formatted = ['radios_formatted'];
		$jobs = ['jobs'];

		$this->_mockRadios
			->expects($this->once())
			->method('getRadios')
			->will($this->returnValue($radios_formatted));

		$this->_mockRadioJob
			->expects($this->once())
			->method('getJobs')
			->will($this->returnValue($jobs));

		$actual_result = $this->_subject->index();

		$expected_result = [
			'radios' => $radios_formatted,
			'radio_jobs' => $jobs,
			'pins' => Radios::$radio_pins,
		];

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testSetStatus() {
		$request  = new Request();
		$radio_id = 10;
		$status   = true;
		$radio_vo = new RadioVO();
		$event    = new RadioChangeEvent($radio_vo, $status);

		$this->_mockRadios
			->expects($this->once())
			->method('getRadio')
			->with($radio_id)
			->will($this->returnValue($radio_vo));

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event);

		$actual_result = $this->_subject->setStatus($request, $radio_id, $status);

		$expected_result = new JsonResponse(true);
		$expected_result->headers->set('X-Flash', json_encode([ControllerInterface::ALERT_SUCCESS, _('Set Radio')]));

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testAddRadio() {
		$name        = 'name';
		$description = 'description';
		$code        = 12;
		$pin_raw     = 'A';
		$pin         = 1;

		$request = new Request();
		$request->request->set('name', $name);
		$request->request->set('description', $description);
		$request->request->set('code', $code);
		$request->request->set('pin', $pin_raw);

		$radio_vo = new RadioVO();
		$radio_vo->name        = $name;
		$radio_vo->description = $description;
		$radio_vo->code        = $code;
		$radio_vo->pin         = $pin;

		$this->_mockRadios
			->expects($this->once())
			->method('addRadio')
			->with($radio_vo);

		$this->_mockRadios
			->expects($this->once())
			->method('getRadioPin')
			->with($pin_raw)
			->will($this->returnValue($pin));

		$actual_result = $this->_subject->addRadio($request);

		$this->assertEquals($radio_vo, $actual_result);
	}

	public function testDeleteRadio() {
		$request = new Request();
		$radio_id = 10;

		$this->_mockRadios
			->expects($this->once())
			->method('deleteRadio')
			->with($radio_id);

		$actual_result = $this->_subject->deleteRadio($request, $radio_id);

		$this->assertTrue($actual_result);
	}

	public function testEditRadio() {
		$radio_id = 10;

		$request = new Request();
		$request->request->set('radio_id', $radio_id);

		$actual_result = $this->_subject->editRadio($request);

		$this->assertTrue($actual_result);
	}

	public function testAddRadioJob() {
		$radio_id    = 10;
		$status      = false;
		$time_string = 'time';

		$radio_vo = new RadioVO();

		$request = new Request();
		$request->request->set('radio_id', $radio_id);
		$request->request->set('status', $status);
		$request->request->set('time', $time_string);

		$this->_mockRadios
			->expects($this->once())
			->method('getRadio')
			->with($radio_id)
			->will($this->returnValue($radio_vo));

		$this->_mockRadioJob
			->expects($this->once())
			->method('addRadioJob')
			->with($radio_vo, $time_string, $status);

		$actual_result = $this->_subject->addRadioJob($request);

		$expected_result = new JsonResponse(true);
		$expected_result->headers->set('X-Flash', json_encode([ControllerInterface::ALERT_SUCCESS, _('The job was sored successfully')]));
		$this->assertEquals($expected_result, $actual_result);
	}

	public function testDeleteRadioJob() {
		$request = new Request();
		$radio_id = 10;

		$this->_mockRadioJob
			->expects($this->once())
			->method('deleteJob')
			->with($radio_id);

		$actual_result = $this->_subject->deleteRadioJob($request, $radio_id);

		$this->assertTrue($actual_result);
	}

}
