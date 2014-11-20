<?php

namespace Tests\Raspberry\Controller\SensorsController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\SensorsController;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\SensorVO;

use Symfony\Component\HttpFoundation\Request;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Raspberry\Sensors\Chart;
use Raspberry\Sensors\SensorBuilder;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @Covers Raspberry\Controller\SensorsController
 */
class SensorsControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorsController
	 */
	private $_subject;

	/**
	 * @var SensorGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorGateway;

	/**
	 * @var SensorValuesGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorValuesGateway;

	/**
	 * @var Chart|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockChart;

	/**
	 * @var SensorBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockSensorBuilder;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockSensorGateway = $this->getMock(SensorGateway::class, [], [], '', false);
		$this->_mockSensorValuesGateway = $this->getMock(SensorValuesGateway::class, [], [], '', false);
		$this->_mockChart = $this->getMock(Chart::class, [], [], '', false);
		$this->_mockSensorBuilder = $this->getMock(SensorBuilder::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new SensorsController($this->_mockSensorGateway, $this->_mockSensorValuesGateway, $this->_mockChart, $this->_mockSensorBuilder);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIndexSensor() {
		$from              = 10;
		$active_sensor_ids = null;
		$last_value        = 100;
		$formatted_value   = '100 grad';
		$type              = 'sensor_type';

		$request = new Request();
		$request->query->set('from', $from);

		$session = new Session(new MockArraySessionStorage());
		$request->setSession($session);

		$sensors_raw = [
			[
				'id' => $sensor_id = 12,
				'last_value' => $last_value,
				'type' => $type,
			]
		];

		$sensors_obj = [
			$type => $sensor = $this->getMock(SensorInterface::class)
		];

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('getSensors')
			->will($this->returnValue($sensors_obj));

		$sensor_ids = [$sensor_id];
		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensorIds')
			->will($this->returnValue($sensor_ids));

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensors')
			->will($this->returnValue($sensors_raw));

		$sensor->expects($this->once())
			->method('formatValue')
			->with($last_value)
			->will($this->returnValue($formatted_value));

		$sensor->expects($this->once())
			->method('getEspeakText')
			->with($last_value)
			->will($this->returnValue($formatted_value));

		$sensor_values = ['values'];
		$sensors_raw[0]['espeak'] = true;
		$sensors_raw[0]['last_value'] = $formatted_value;

		$this->_mockSensorValuesGateway
			->expects($this->once())
			->method('getSensorValues')
			->with($sensor_id, $from)
			->will($this->returnValue($sensor_values));

		$json = ['json'];
		$this->_mockChart
			->expects($this->once())
			->method('formatJsonData')
			->with($sensors_raw, [$sensor_id => $sensor_values])
			->will($this->returnValue($json));

		$actual_result = $this->_subject->indexSensor($request, $active_sensor_ids);

		$expected_result = [
			'sensors' => $sensors_raw,
			 'active_sensor_ids' => $sensor_ids,
			 'json' => $json,
			 'current_from' => $from,
			 'available_sensors' => $sensors_obj,
			 'from_intervals' => [
				 0 => 'All',
				 3600 => 'Last Hour',
				 86400 => 'Last Day',
				 86400 * 7 => 'Last Week',
				 86400 * 30 => 'Last Month'
			 ]];

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testIndexSensorWithoutFromAndLastValue() {
		$from              = null;
		$active_sensor_ids = null;
		$last_value        = null;
		$type              = 'sensor_type';

		$request = new Request();
		$request->query->set('from', $from);

		$session = new Session(new MockArraySessionStorage());
		$request->setSession($session);

		$sensors_raw = [
			[
				'id' => $sensor_id = 12,
				'last_value' => $last_value,
				'type' => $type,
			]
		];

		$sensors_obj = [
			$type => $sensor = $this->getMock(SensorInterface::class)
		];

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('getSensors')
			->will($this->returnValue($sensors_obj));

		$sensor_ids = [$sensor_id];
		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensorIds')
			->will($this->returnValue($sensor_ids));

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensors')
			->will($this->returnValue($sensors_raw));

		$sensors_raw[0]['espeak'] = false;

		$json = ['json'];
		$this->_mockChart
			->expects($this->once())
			->method('formatJsonData')
			->with($sensors_raw, [])
			->will($this->returnValue($json));

		$actual_result = $this->_subject->indexSensor($request, "13");

		$expected_result = [
			'sensors' => $sensors_raw,
			 'active_sensor_ids' => [13],
			 'json' => $json,
			 'current_from' => Chart::DEFAULT_TIME,
			 'available_sensors' => $sensors_obj,
			 'from_intervals' => [
				 0 => 'All',
				 3600 => 'Last Hour',
				 86400 => 'Last Day',
				 86400 * 7 => 'Last Week',
				 86400 * 30 => 'Last Month'
			 ]];

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testAddSensor() {
		$type = 'type';
		$name        = 'name';
		$description = 'descritpion';
		$pin         = 'pin';
		$interval    = 12;
		$node        = 1;

		$request = new Request();
		$request->request->set('type', $type);
		$request->request->set('name', $name);
		$request->request->set('description', $description);
		$request->request->set('pin', $pin);
		$request->request->set('interval', $interval);
		$request->request->set('node', $node);

		$sensor_vo = new SensorVO();
		$sensor_vo->name        = $name;
		$sensor_vo->type        = $type;
		$sensor_vo->description = $description;
		$sensor_vo->pin         = $pin;
		$sensor_vo->interval    = $interval;
		$sensor_vo->node        = $node;

		$this->_mockSensorGateway
			->expects($this->once())
			->method('addSensor')
			->with($sensor_vo);

		$actual_result = $this->_subject->addSensor($request);

		$this->assertEquals($sensor_vo, $actual_result);
	}

	public function testEspeak() {
		$request = new Request();
		$sensor_id = 10;
		$espeak_text = 'last value';

		$sensor = [
			'type' => $sensor_type = 'type',
			'last_value' => $last_value = 'last value',
		];

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensor')
			->with($sensor_id)
			->will($this->returnValue($sensor));

		$mock_sensor = $this->getMockForAbstractClass(SensorInterface::class, ['getEspeakText']);

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('build')
			->with($sensor_type)
			->will($this->returnValue($mock_sensor));

		$mock_sensor
			->expects($this->once())
			->method('getEspeakText')
			->will($this->returnValue($espeak_text));

		$espeak_vo = new EspeakVO($espeak_text);
		$event = new EspeakEvent($espeak_vo);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground')
			->with($event);

		$actual_result = $this->_subject->espeak($request, $sensor_id);

		$this->assertTrue($actual_result);
	}

	public function testSlim() {
		$sensor_id              = 12;
		$sensor_value_formatted = 100;
		$sensor_value           = '100 grad';
		$type                   = 'sensor type';

		$request = new Request();

		$sensor_obj = $this->getMock(SensorInterface::class);
		$sensor_raw = [
			'type'       => $type,
			'last_value' => $sensor_value
		];

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensor')
			->with($sensor_id)
			->will($this->returnValue($sensor_raw));

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('build')
			->with($type)
			->will($this->returnValue($sensor_obj));

		$sensor_obj
			->expects($this->once())
			->method('getEspeakText')
			->with($sensor_value)
			->will($this->returnValue($sensor_value_formatted));

		$actual_result = $this->_subject->slim($request, $sensor_id);

		$expected_value = [
		   'sensor' => $sensor_raw,
		   'sensor_value_formatted' => $sensor_value_formatted,
		   'sensor_obj' => $sensor_obj,
		   'refresh_interval' => 60
	   ];

		$this->assertEquals($expected_value, $actual_result);
	}

	public function testGetValue() {
		$sensor_id              = 12;
		$sensor_value_formatted = 100;
		$sensor_value           = '100 grad';
		$type                   = 'sensor type';

		$request = new Request();
		$request->query->set('sensor_id', $sensor_id);

		$sensor_obj = $this->getMock(SensorInterface::class);
		$sensor_raw = [
			'type'       => $type,
			'last_value' => $sensor_value
		];

		$this->_mockSensorGateway
			->expects($this->once())
			->method('getSensor')
			->with($sensor_id)
			->will($this->returnValue($sensor_raw));

		$this->_mockSensorBuilder
			->expects($this->once())
			->method('build')
			->with($type)
			->will($this->returnValue($sensor_obj));

		$sensor_obj
			->expects($this->once())
			->method('getEspeakText')
			->with($sensor_value)
			->will($this->returnValue($sensor_value_formatted));

		$actual_result = $this->_subject->getValue($request);

		$expected_value = [
		   'sensor' => $sensor_raw,
		   'sensor_value_formatted' => $sensor_value_formatted,
		   'sensor_obj' => $sensor_obj,
		   'refresh_interval' => 60
		];

		$this->assertEquals($expected_value, $actual_result);
	}

}
