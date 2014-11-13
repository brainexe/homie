<?php

namespace Tests\Raspberry\Controller\SensorsController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\SensorsController;
use Symfony\Component\HttpFoundation\Request;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\Chart;
use Raspberry\Sensors\SensorBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Controller\SensorsController
 */
class SensorsControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorsController
	 */
	private $_subject;

	/**
	 * @var SensorGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorGateway;

	/**
	 * @var SensorValuesGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorValuesGateway;

	/**
	 * @var Chart|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockChart;

	/**
	 * @var SensorBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorBuilder;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {

		$this->_mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
		$this->_mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
		$this->_mockChart = $this->getMock(Chart::class, [], [], '', false);
		$this->_mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_subject = new SensorsController($this->_mockSensorGateway, $this->_mockSensorValuesGateway, $this->_mockChart, $this->_mockSensorBuilder);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->index($request);
	}

	public function testIndexSensor() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$active_sensor_ids = null;
		$actual_result = $this->_subject->indexSensor($request, $active_sensor_ids);
	}

	public function testAddSensor() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->addSensor($request);
	}

	public function testEspeak() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$sensor_id = null;
		$actual_result = $this->_subject->espeak($request, $sensor_id);
	}

	public function testSlim() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$sensor_id = null;
		$actual_result = $this->_subject->slim($request, $sensor_id);
	}

	public function testGetValue() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$request = new Request();
		$actual_result = $this->_subject->getValue($request);
	}

}
