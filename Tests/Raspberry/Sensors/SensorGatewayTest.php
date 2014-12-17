<?php

namespace Tests\Raspberry\Sensors\SensorGateway;

use BrainExe\Core\Redis\Redis;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorVO;

/**
 * @Covers Raspberry\Sensors\SensorGateway
 */
class SensorGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorGateway
	 */
	private $subject;

	/**
	 * @var Redis|MockObject
	 */
	private $mockRedis;


	public function setUp() {
		$this->mockRedis = $this->getMock(Redis::class, [], [], '', false);

		$this->subject = new SensorGateway();
		$this->subject->setRedis($this->mockRedis);
	}

	public function testGetSensors() {
		$sensor_ids = [
			$sensor_id = 10
		];

		$result = ['result'];

		$this->mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$this->mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("sensor:$sensor_id");

		$this->mockRedis
			->expects($this->once())
			->method('exec')
			->will($this->returnValue($result));

		$actual_result = $this->subject->getSensors();

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

		$this->mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$this->mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("sensor:$sensor_id");

		$this->mockRedis
			->expects($this->once())
			->method('exec')
			->will($this->returnValue($result));

		$actual_result = $this->subject->getSensorsForNode($node);

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

		$this->mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$actual_result = $this->subject->getSensorIds();

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

		$this->mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(SensorGateway::SENSOR_IDS)
			->will($this->returnValue($sensor_ids));

		$this->mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('HMSET')
			->with("sensor:$new_sensor_id");

		$this->mockRedis
			->expects($this->once())
			->method('sAdd')
			->with(SensorGateway::SENSOR_IDS, $new_sensor_id);

		$this->mockRedis
			->expects($this->once())
			->method('exec');

		$actual_result = $this->subject->addSensor($sensor_vo);

		$this->assertEquals($new_sensor_id, $actual_result);
	}

	public function testGetSensor() {
		$sensor_id = 10;
		$sensor = ['sensor'];

		$this->mockRedis
			->expects($this->once())
			->method('hGetAll')
			->with("sensor:$sensor_id")
			->will($this->returnValue($sensor));

		$actual_result = $this->subject->getSensor($sensor_id);

		$this->assertEquals($sensor, $actual_result);
	}

	public function testDeleteSensor() {
		$sensor_id = 10;

		$this->mockRedis
			->expects($this->at(0))
			->method('del')
			->with("sensor:$sensor_id");

		$this->mockRedis
			->expects($this->at(1))
			->method('sRem')
			->with(SensorGateway::SENSOR_IDS, $sensor_id);

		$this->mockRedis
			->expects($this->at(2))
			->method('del')
			->with("sensor_values:$sensor_id");

		$this->subject->deleteSensor($sensor_id);
	}

}
