<?php

namespace Tests\Raspberry\Console\SensorAddCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Console\SensorAddCommand;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorBuilder;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @Covers Raspberry\Console\SensorAddCommand
 */
class SensorAddCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorAddCommand
	 */
	private $_subject;

	/**
	 * @var SensorGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorGateway;

	/**
	 * @var SensorBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorBuilder;

	public function setUp() {
		$this->_mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
		$this->_mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);
		$this->_subject = new SensorAddCommand($this->_mockSensorGateway, $this->_mockSensorBuilder);
	}

	public function testExecute() {
		$input = new ArrayInput([]);
		$output = new DummyOutput();

//		$this->_subject->execute($input, $output);
	}

}
