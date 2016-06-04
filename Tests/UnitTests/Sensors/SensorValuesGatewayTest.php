<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Core\Util\Time;
use BrainExe\Tests\RedisMockTrait;
use Homie\Sensors\SensorVO;
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

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->time  = $this->createMock(Time::class);
        $this->idGenerator  = $this->createMock(IdGenerator::class);

        $this->subject = new SensorValuesGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setTime($this->time);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testAddValue()
    {
        $sensorId = 10;
        $value    = 100;
        $now      = 10000;
        $valueId  = 4242;

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);
        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($valueId);

        $this->redis
            ->expects($this->once())
            ->method('zadd')
            ->with("sensor_values:$sensorId", ["$valueId-$value" => $now]);

        $this->redis
            ->expects($this->once())
            ->method('hmset')
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

        $sensorVo = new SensorVO();
        $sensorVo->sensorId = $sensorId;
        $this->subject->addValue($sensorVo, $value);
    }

    public function testGetSensorValues()
    {
        $sensorId = 10;
        $from     = 300;
        $to       = 1000;

        $redisResult = [
            "701-100" => 701,
            "702-101" => 702,
            "703--1"  => 703,
        ];
        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with("sensor_values:$sensorId", 300, $to)
            ->willReturn($redisResult);

        $actual = $this->subject->getSensorValues($sensorId, $from, $to);

        $expected = [
            701 => 100,
            702 => 101,
            703 => -1,
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetByTime()
    {
        $time     = 300;

        $redisResult = [
            ["701-100"],
            ["702-101"],
            ["703--1"],
        ];
        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($redisResult);

        $actual = $this->subject->getByTime([11, 12, 13], $time);

        $expected = [
            11 => 100.0,
            12 => 101.0,
            13 => -1.0,
        ];

        $this->assertEquals($expected, iterator_to_array($actual));
    }

    public function testGetAllSensorValuesWithEmptySet()
    {
        $sensorId = 10;
        $from     = 0;
        $to       = 1000;

        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with("sensor_values:$sensorId", 0, $to)
            ->willReturn([]);

        $actual = $this->subject->getSensorValues($sensorId, $from, $to);

        $expected = [];

        $this->assertEquals($expected, $actual);
    }
}
