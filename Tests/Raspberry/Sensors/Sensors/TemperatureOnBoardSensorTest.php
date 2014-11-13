<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureOnBoardSensor
 */
class TemperatureOnBoardSensorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TemperatureOnBoardSensor
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TemperatureOnBoardSensor();
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(TemperatureOnBoardSensor::TYPE, $actual_result);
	}

	public function testGetValue() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->getValue($pin);
	}

	public function testIsSupported() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->isSupported($output);
	}

}
