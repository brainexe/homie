<?php

namespace Tests\Raspberry\Console\SensorCronCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Console\SensorCronCommand;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorValueEvent;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\SensorVO;
use Raspberry\Sensors\SensorVOBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Monolog\Logger;
use BrainExe\Core\Util\Time;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Console\SensorCronCommand
 */
class SensorCronCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorCronCommand
	 */
	private $_subject;

	/**
	 * @var SensorGateway|MockObject
	 */
	private $_mockSensorGateway;

	/**
	 * @var SensorValuesGateway|MockObject
	 */
	private $_mockSensorValuesGateway;

	/**
	 * @var SensorBuilder|MockObject
	 */
	private $_mockSensorBuilder;

	/**
	 * @var SensorVOBuilder|MockObject
	 */
	private $_mockSensorVOBuilder;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $_mockEventDispatcher;

	/**
	 * @var Logger|MockObject
	 */
	private $_mockLogger;

	/**
	 * @var Time|MockObject
	 */
	private $_mockTime;

	/**
	 * @var integer
	 */
	private $_nodeId = 1;

	public function setUp() {
		$this->_mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
		$this->_mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
		$this->_mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);
		$this->_mockSensorVOBuilder = $this->getMock(SensorVOBuilder::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_mockLogger = $this->getMock(Logger::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);

		$this->_subject = new SensorCronCommand($this->_mockSensorGateway, $this->_mockSensorValuesGateway, $this->_mockSensorBuilder, $this->_mockSensorVOBuilder, $this->_mockEventDispatcher, $this->_nodeId);
		$this->_subject->setLogger($this->_mockLogger);
		$this->_subject->setTime($this->_mockTime);
	}

	public function testExecuteWithEmptyValue() {
		$application = new Application();
		$application->setAutoExit(false);
		$application->add($this->_subject);

		$now = 1000;
		$sensors = [
			$sensor_raw = [
			]
		];

		$sensor = new SensorVO();
		$sensor->id = 10;
		$sensor->type = $type = 'type';
		$sensor->pin = $pin = 12;
		$sensor->name = $name = 'name';

		$current_sensor_value = null;

		$sensor_object = $this->getMockForAbstractClass(SensorInterface::class);
		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensors')
			->with($this->_nodeId)
			->will($this->returnValue($sensors));

		$this->_mockSensorVOBuilder
			->expects($this->once())
			->method('buildFromArray')
			->with($sensor_raw)
			->will($this->returnValue($sensor));

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('build')
			->with($type)
			->will($this->returnValue($sensor_object));

		$sensor_object
			->expects($this->once())
			->method('getValue')
			->with($pin)
			->will($this->returnValue($current_sensor_value));

		$input = ['--force'];
		$command_tester = new CommandTester($this->_subject);
		$command_tester->execute($input);

		$output = $command_tester->getDisplay();
		$this->assertEquals("Invalid sensor value: #10 type (name)\n", $output);
	}
	public function testExecute() {
		$now = 1000;
		$sensors = [
			$sensor_raw = [
			]
		];

		$sensor = new SensorVO();
		$sensor->id = 10;
		$sensor->type = $type = 'type';
		$sensor->pin = $pin = 12;
		$sensor->name = $name = 'name';

		$current_sensor_value = 1000;
		$formatted_sensor_value = "1000 grad";

		/** @var SensorInterface|MockObject $sensor_object */
		$sensor_object = $this->getMockForAbstractClass(SensorInterface::class);
		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensors')
			->with($this->_nodeId)
			->will($this->returnValue($sensors));

		$this->_mockSensorVOBuilder
			->expects($this->once())
			->method('buildFromArray')
			->with($sensor_raw)
			->will($this->returnValue($sensor));

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('build')
			->with($type)
			->will($this->returnValue($sensor_object));

		$sensor_object
			->expects($this->once())
			->method('getValue')
			->with($pin)
			->will($this->returnValue($current_sensor_value));

		$sensor_object
			->expects($this->once())
			->method('formatValue')
			->with($current_sensor_value)
			->will($this->returnValue($formatted_sensor_value));

		$event = new SensorValueEvent(
			$sensor,
			$sensor_object,
			$current_sensor_value,
			$formatted_sensor_value,
			$now
		);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$application = new Application();
		$application->add($this->_subject);
		$input = ['--force'];
		$command_tester = new CommandTester($this->_subject);
		$command_tester->execute($input);

		$output = $command_tester->getDisplay();
		$this->assertEquals("Sensor value: #10 type (name): 1000 grad\n", $output);
	}

}
