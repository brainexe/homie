<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureDHT11Sensor
 */
class TemperatureDHT11SensorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TemperatureDHT11Sensor
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TemperatureDHT11Sensor();
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(TemperatureDHT11Sensor::TYPE, $actual_result);
	}

	public function testGetValue() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getValue($pin);
	}

	public function testFormatValue() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->formatValue($value);
	}

	public function testGetEspeakText() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->getEspeakText($value);
	}

}
