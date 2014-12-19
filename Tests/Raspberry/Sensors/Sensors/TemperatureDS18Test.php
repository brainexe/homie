<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureDS18;

use BrainExe\Core\Util\FileSystem;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\Sensors\TemperatureDS18;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureDS18
 */
class TemperatureDS18Test extends PHPUnit_Framework_TestCase {

	/**
	 * @var TemperatureDS18
	 */
	private $_subject;

	/**
	 * @var FileSystem|MockObject
	 */
	private $_mockFileSystem;

	public function setUp() {
		$this->_mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

		$this->_subject = new TemperatureDS18($this->_mockFileSystem);
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(TemperatureDS18::TYPE, $actual_result);
	}

	public function testGetValueWhenNotSupported() {
		$pin = 12;

		$file = sprintf(TemperatureDS18::PIN_FILE, $pin);

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(false));

		$actual_result = $this->_subject->getValue($pin);

		$this->assertNull($actual_result);
	}

	/**
	 * @param string $content
	 * @param string|null $expected_result
	 * @dataProvider provideContent
	 */
	public function testGetValue($content, $expected_result) {
		$pin = 12;

		$file = sprintf(TemperatureDS18::PIN_FILE, $pin);

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(true));

		$this->_mockFileSystem
			->expects($this->once())
			->method('fileGetContents')
			->with($file)
			->will($this->returnValue($content));

		$actual_result = $this->_subject->getValue($pin);

		$this->assertEquals($expected_result, $actual_result);
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
		$file = sprintf(TemperatureDS18::BUS_DIR);

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(true));

		$output = new DummyOutput();
		$actual_result = $this->_subject->isSupported($output);

		$this->assertTrue($actual_result);
	}

	public function testIsSupportedWhenNotSupported() {
		$file = sprintf(TemperatureDS18::BUS_DIR);

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(false));

		$output = new DummyOutput();
		$actual_result = $this->_subject->isSupported($output);

		$this->assertFalse($actual_result);
	}

	/**
	 * @return array[]
	 */
	public function provideFormatValues() {
		return [
			[100, '100.00°'],
			[0, '0.00°'],
			['', '0.00°'],
			[10.2222, '10.22°'],
			[-10.2222, '-10.22°'],
		];
	}

	/**
	 * @return array[]
	 */
	public function provideEspeakText() {
		return [
			[100, '100,0 Degree'],
			[0, '0,0 Degree'],
			['', '0,0 Degree'],
			[10.2222, '10,2 Degree'],
			[-10.2222, '-10,2 Degree'],
		];
	}

	/**
	 * @return array[]
	 */
	public function provideContent() {
		return [
			['', null],
			['invalid', null],
			['YES foo', null],
			['YES t=0', null],
			['YES t=85000', null],
			['YES t=70000', 70]
			,
			['YES t=70001', 70.001],
		];
	}
}
