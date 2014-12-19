<?php

namespace Tests\Raspberry\Sensors\SensorValuesGateway;

use BrainExe\Core\Redis\Redis;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;

/**
 * @Covers Raspberry\Sensors\SensorValuesGateway
 */
class SensorValuesGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorValuesGateway
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new SensorValuesGateway();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setTime($this->mockTime);
    }

    public function testAddValue()
    {
        $sensor_id = 10;
        $value     = 100;
        $now       = 10000;

        $this->mockRedis
        ->expects($this->once())
        ->method('multi')
        ->will($this->returnValue($this->mockRedis));

        $this->mockRedis
        ->expects($this->once())
        ->method('ZADD')
        ->with("sensor_values:$sensor_id", $now, "$now-$value");

        $this->mockRedis
        ->expects($this->once())
        ->method('HMSET')
        ->with(SensorGateway::REDIS_SENSOR_PREFIX . $sensor_id, [
        'last_value' => $value,
        'last_value_timestamp' => $now
        ]);

        $this->mockRedis
        ->expects($this->once())
        ->method('exec');

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $this->subject->addValue($sensor_id, $value);
    }

    public function testGetSensorValues()
    {
        $sensor_id = 10;
        $from = 300;
        $now = 1000;

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $redis_result = [
        "701-100",
        "702-101",
        ];
        $this->mockRedis
        ->expects($this->once())
        ->method('ZRANGEBYSCORE')
        ->with("sensor_values:$sensor_id", 700, $now)
        ->will($this->returnValue($redis_result));

        $actual_result = $this->subject->getSensorValues($sensor_id, $from);

        $expected_result = [
        701 => 100,
        702 => 101,
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testDeleteOldValues()
    {
        $sensor_id = 10;
        $days = 1;
        $now = 86410;
        $deleted_percent = 80;

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $old_values = [
        "701-100",
        "702-101",
        ];

        $this->mockRedis
        ->expects($this->at(0))
        ->method('ZRANGEBYSCORE')
        ->with("sensor_values:$sensor_id", 0, 10)
        ->will($this->returnValue($old_values));

        $this->mockRedis
        ->expects($this->at(1))
        ->method('ZREM')
        ->with("sensor_values:$sensor_id", "701-100");

        $this->mockRedis
        ->expects($this->at(2))
        ->method('ZREM')
        ->with("sensor_values:$sensor_id", "702-101");

        $actual_result = $this->subject->deleteOldValues($sensor_id, $days, $deleted_percent);

        $this->assertEquals(2, $actual_result);

    }
}
