<?php

namespace Tests\Raspberry\Sensors\SensorGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\SensorGateway;

use Raspberry\Sensors\SensorVO;
use Redis;

/**
 * @Covers Raspberry\Sensors\SensorGateway
 */
class SensorGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;


	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->_subject = new SensorGateway();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetSensors() {
		$sensor_ids = [
			$sensor_id = 10
		];

		$result = ['result'];

		$this->_mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$this->_mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("sensor:$sensor_id");

		$this->_mockRedis
			->expects($this->once())
			->method('exec')
			->will($this->returnValue($result));

		$actual_result = $this->_subject->getSensors();

		$this->assertEquals($result, $actual_result);
	}

	public function testGetSensorsForNode() {
		$node = 1;
		$sensor_ids = [
			$sensor_id = 10
		];

		$result = [
			[
				'node' => 100
			],
			[
				'node' => $node
			]
		];

		$this->_mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$this->_mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("sensor:$sensor_id");

		$this->_mockRedis
			->expects($this->once())
			->method('exec')
			->will($this->returnValue($result));

		$actual_result = $this->_subject->getSensorsForNode($node);

		$expected_result = [
			1 => [
				'node' => $node
			]
		];
		$this->assertEquals($expected_result, $actual_result);
	}

	public function testGetSensorIds() {
		$sensor_ids = [
			$sensor_id = 10
		];

		$this->_mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$actual_result = $this->_subject->getSensorIds();

		$this->assertEquals($sensor_ids, $actual_result);
	}

	public function testAddSensor() {
		$sensor_vo = new SensorVO();
		$sensor_ids = [
			$last_sensor_id = 10
		];

		$new_sensor_id = 11;

		$sensor_data = (array)$sensor_vo;
		$sensor_data['id'] = $new_sensor_id;
		$sensor_data['last_value'] = 0;
		$sensor_data['last_value_timestamp'] = 0;

		$this->_mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$this->_mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('HMSET')
			->with("sensor:$new_sensor_id");

		$this->_mockRedis
			->expects($this->once())
			->method('sAdd')
			->with(SensorGateway::SENSOR_IDS, $new_sensor_id);

		$this->_mockRedis
			->expects($this->once())
			->method('exec');

		$actual_result = $this->_subject->addSensor($sensor_vo);

		$this->assertEquals($new_sensor_id, $actual_result);
	}

	public function testGetSensor() {
		$sensor_id = 10;
		$sensor = ['sensor'];

		$this->_mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with("sensor:$sensor_id")
			->will($this->returnValue($sensor));

		$actual_result = $this->_subject->getSensor($sensor_id);

		$this->assertEquals($sensor, $actual_result);
	}

	public function testDeleteSensor() {
		$sensor_id = 10;

		$this->_mockRedis
			->expects($this->at(0))
			->method('del')
			->with("sensor:$sensor_id");

		$this->_mockRedis
			->expects($this->at(1))
			->method('sRem')
			->with(SensorGateway::SENSOR_IDS, $sensor_id);

		$this->_mockRedis
			->expects($this->at(2))
			->method('del')
			->with("sensor_values:$sensor_id");

		$this->_subject->deleteSensor($sensor_id);
	}

}
