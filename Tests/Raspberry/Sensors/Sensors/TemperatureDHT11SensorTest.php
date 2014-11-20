<?php

namespace Tests\Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class TemperatureDHT11SensorTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TemperatureDHT11Sensor
	 */
	private $_subject;

	/**
	 * @var ProcessBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockProcessBuilder;

	/**
	 * @var Filesystem|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockFileSystem;

	public function setUp() {
		$this->_mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
		$this->_mockFileSystem = $this->getMock(Filesystem::class, [], [], '', false);

		$this->_subject = new TemperatureDHT11Sensor($this->_mockProcessBuilder, $this->_mockFileSystem);
	}

	public function testGetSensorType() {
		$actual_result = $this->_subject->getSensorType();

		$this->assertEquals(TemperatureDHT11Sensor::TYPE, $actual_result);
	}
	public function testGetValueWitInvalidOutput() {
		$pin = 3;

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process->expects($this->once())
			->method('run');

		$process->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(false));

		$actual_result = $this->_subject->getValue($pin);

		$this->assertNull($actual_result);
	}

	public function testGetValueWitValidOutput() {
		$temp = 70;
		$pin   = 3;

		$output = "Temp = $temp %";

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process->expects($this->once())
			->method('run');

		$process->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(true));

		$process->expects($this->once())
			->method('getOutput')
			->will($this->returnValue($output));

		$actual_result = $this->_subject->getValue($pin);

		$this->assertEquals($temp, $actual_result);
	}
}
