<?php

namespace Tests\Raspberry\Sensors\SensorValuesGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\SensorValuesGateway;
use Redis;

/**
 * @Covers Raspberry\Sensors\SensorValuesGateway
 */
class SensorValuesGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorValuesGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;


	public function setUp() {
		parent::setUp();

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new SensorValuesGateway();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testAddValue() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->addValue($sensor_id, $value);
	}

	public function testGetSensorValues() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getSensorValues($sensor_id, $from);
	}

	public function testDeleteOldValues() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteOldValues($sensor_id, $days, $deleted_percent);
	}

}
