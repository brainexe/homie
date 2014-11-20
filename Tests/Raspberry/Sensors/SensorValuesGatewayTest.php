<?php

namespace Tests\Raspberry\Sensors\SensorValuesGateway;

use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;
use Redis;

/**
 * @Covers Raspberry\Sensors\SensorValuesGateway
 */
class SensorValuesGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SensorValuesGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var Time|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTime;

	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);

		$this->_subject = new SensorValuesGateway();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setTime($this->_mockTime);
	}

	public function testAddValue() {
		$sensor_id = 10;
		$value     = 100;
		$now       = 10000;

		$this->_mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->_mockRedis));

		$this->_mockRedis
			->expects($this->once())
			->method('ZADD')
			->with("sensor_values:$sensor_id", $now, "$now-$value");

		$this->_mockRedis
			->expects($this->once())
			->method('HMSET')
			->with(SensorGateway::REDIS_SENSOR_PREFIX . $sensor_id, [
				'last_value' => $value,
				'last_value_timestamp' => $now
			]);

		$this->_mockRedis
			->expects($this->once())
			->method('exec');

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$this->_subject->addValue($sensor_id, $value);
	}

	public function testGetSensorValues() {
		$sensor_id = 10;
		$from = 300;
		$now = 1000;

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$redis_result = [
			"701-100",
			"702-101",
		];
		$this->_mockRedis
			->expects($this->once())
			->method('ZRANGEBYSCORE')
			->with("sensor_values:$sensor_id", 700, $now)
			->will($this->returnValue($redis_result));

		$actual_result = $this->_subject->getSensorValues($sensor_id, $from);

		$expected_result = [
			701 => 100,
			702 => 101,
		];

		$this->assertEquals($expected_result, $actual_result);
	}

	public function testDeleteOldValues() {
		$sensor_id = 10;
		$days = 1;
		$now = 86410;
		$deleted_percent = 80;

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$old_values = [
			"701-100",
			"702-101",
		];

		$this->_mockRedis
			->expects($this->at(0))
			->method('ZRANGEBYSCORE')
			->with("sensor_values:$sensor_id", 0, 10)
			->will($this->returnValue($old_values));

		$this->_mockRedis
			->expects($this->at(1))
			->method('ZREM')
			->with("sensor_values:$sensor_id", "701-100");

		$this->_mockRedis
			->expects($this->at(2))
			->method('ZREM')
			->with("sensor_values:$sensor_id", "702-101");

		$actual_result = $this->_subject->deleteOldValues($sensor_id, $days, $deleted_percent);

		$this->assertEquals(2, $actual_result);

	}

}
