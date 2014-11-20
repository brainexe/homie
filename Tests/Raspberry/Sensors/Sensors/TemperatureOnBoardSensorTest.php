<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Covers Raspberry\Sensors\Sensors\TemperatureOnBoardSensor
 */
class TemperatureOnBoardSensorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TemperatureOnBoardSensor
	 */
	private $_subject;

	/**
	 * @var FileSystem|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockFileSystem;

	public function setUp() {
		$this->_mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

		$this->_subject = new TemperatureOnBoardSensor($this->_mockFileSystem);
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
		$file = TemperatureOnBoardSensor::PATH;

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
		$file = TemperatureOnBoardSensor::PATH;

		$this->_mockFileSystem
			->expects($this->once())
			->method('exists')
			->with($file)
			->will($this->returnValue(false));

		$output = new DummyOutput();
		$actual_result = $this->_subject->isSupported($output);

		$this->assertFalse($actual_result);
	}

}
