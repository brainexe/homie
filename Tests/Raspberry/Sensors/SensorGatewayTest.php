<?php

namespace Tests\Raspberry\Sensors\SensorGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\SensorGateway;
use Redis;

/**
 * @Covers Raspberry\Sensors\SensorGateway
 */
class SensorGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;


	public function setUp() {
		parent::setUp();

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new SensorGateway();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetSensors() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getSensors();
	}

	public function testGetSensorsForNode() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getSensorsForNode($node_id);
	}

	public function testGetSensorIds() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getSensorIds();
	}

	public function testAddSensor() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addSensor($sensor_vo);
	}

	public function testGetSensor() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getSensor($sensor_id);
	}

	public function testDeleteSensor() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->deleteSensor($sensor_id);
	}

}
