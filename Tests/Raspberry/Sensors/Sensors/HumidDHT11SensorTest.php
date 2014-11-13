<?php

namespace Tests\Raspberry\Sensors\Sensors\HumidDHT11Sensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\HumidDHT11Sensor;

/**
 * @Covers Raspberry\Sensors\Sensors\HumidDHT11Sensor
 */
class HumidDHT11SensorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var HumidDHT11Sensor
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new HumidDHT11Sensor();
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(HumidDHT11Sensor::TYPE, $actual_result);
	}

	public function testGetValue() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getValue($pin);
	}

	/**
	 * @param float $given
	 * @param string $expected_result
	 * @dataProvider provideFormatValues
	 */
	public function testFormatValue($given, $expected_result) {
		$actual_result = $this->_subject->formatValue($given);

		$this->assertEquals($expected_result, $actual_result);
	}
	/**
	 * @param float $given
	 * @param string $expected_result
	 * @dataProvider provideEspeakText
	 */
	public function testGetEspeakText($given, $expected_result) {
		$actual_result = $this->_subject->getEspeakText($given);

		$this->assertEquals($expected_result, $actual_result);
	}

	public function provideEspeakText() {
		return [
			[100, '100 Percent'],
			[0, '0 Percent'],
			['', '0 Percent'],
			[10.2222, '10 Percent'],
			[-10.2222, '-10 Percent'],
		];
	}

	public function provideFormatValues() {
		return [
			[100, '100%'],
			[0, '0%'],
			['', '0%'],
			[10.2222, '10%'],
			[-10.2222, '-10%'],
		];
	}

}
