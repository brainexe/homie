<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\Time;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;

/**
 * @covers Homie\Sensors\SensorValuesGateway
 */
class SensorValuesGatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var SensorValuesGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->time  = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new SensorValuesGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setTime($this->time);
    }

    public function testAddValue()
    {
        $sensorId = 10;
        $value    = 100;
        $now      = 10000;

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('ZADD')
            ->with("sensor_values:$sensorId", $now, "$now-$value");

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with(SensorGateway::REDIS_SENSOR_PREFIX . $sensorId, [
                'lastValue' => $value,
                'lastValueTimestamp' => $now
            ]);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->subject->addValue($sensorId, $value);
    }

    public function testGetSensorValues()
    {
        $sensorId = 10;
        $from     = 300;
        $now      = 1000;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $redisResult = [
            "701-100",
            "702-101",
        ];
        $this->redis
            ->expects($this->once())
            ->method('ZRANGEBYSCORE')
            ->with("sensor_values:$sensorId", 700, $now)
            ->willReturn($redisResult);

        $actualResult = $this->subject->getSensorValues($sensorId, $from);

        $expectedResult = [
            701 => 100,
            702 => 101,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteOldValues()
    {
        $sensorId = 10;
        $now      = 86410;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $oldValues = [
            "701-100" => 10000,
            "702-101" => 10001,
            "702-103" => 2330000,
        ];

        $this->redis
            ->expects($this->once())
            ->method('ZRANGEBYSCORE')
            ->with("sensor_values:$sensorId", 0, $now - 86400, ['withscores' => true])
            ->willReturn($oldValues);

        $this->redis
            ->expects($this->once())
            ->method('ZREM')
            ->with("sensor_values:$sensorId", "702-101");

        $actual = $this->subject->deleteOldValues($sensorId);

        $this->assertEquals(1, $actual);
    }
}
