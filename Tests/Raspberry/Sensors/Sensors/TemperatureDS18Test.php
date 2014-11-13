<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureDS18;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\TemperatureDS18;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureDS18
 */
class TemperatureDS18Test extends PHPUnit_Framework_TestCase {

	/**
	 * @var TemperatureDS18
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new TemperatureDS18();
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(TemperatureDS18::TYPE, $actual_result);
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

	public function testIsSupported() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->isSupported($output);
	}

	public function provideFormatValues() {
		return [
			[100, '100.00°'],
			[0, '0.00°'],
			['', '0.00°'],
			[10.2222, '10.22°'],
			[-10.2222, '-10.22°'],
		];
	}

	public function provideEspeakText() {
		return [
			[100, '100,0 Degree'],
			[0, '0,0 Degree'],
			['', '0,0 Degree'],
			[10.2222, '10,2 Degree'],
			[-10.2222, '-10,2 Degree'],
		];
	}

}
