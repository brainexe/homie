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
        $this->time  = $this->getMock(Time::class, [], [], '', false);
        $this->idGenerator  = $this->getMock(IdGenerator::class, [], [], '', false);

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
            ->with("sensor_values:$sensorId", [$now => "$valueId-$value"]);

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
        $now      = 1000;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $redisResult = [
            "701-100" => 701,
            "702-101" => 702,
            "703--1"  => 703,
        ];
        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with("sensor_values:$sensorId", 700, $now)
            ->willReturn($redisResult);

        $actual = $this->subject->getSensorValues($sensorId, $from);

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
        $from     = -1;
        $now      = 1000;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->redis
            ->expects($this->once())
            ->method('zrangebyscore')
            ->with("sensor_values:$sensorId", 0, $now)
            ->willReturn([]);

        $actualResult = $this->subject->getSensorValues($sensorId, $from);

        $expectedResult = [];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteOldValues()
    {
        $sensorId = 10;
        $now      = 3 * 86400 + 10;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $oldValues = [
            "701-100" => 10000,
            "702-101" => 10001,
            "702-103" => 2330000,
            "400-103" => 2330000,
            "4334702-103" => 2330000,
        ];

        $this->redis
            ->expects($this->exactly(count(SensorValuesGateway::FRAMES)))
            ->method('zrangebyscore')
            ->willReturn($oldValues);

        $this->redis
            ->expects($this->exactly(7))
            ->method('zrem');

        $actual = $this->subject->deleteOldValues($sensorId);

        $this->assertEquals(7, $actual);
    }

    public function testDeleteOldValuesWithoutValues()
    {
        $sensorId = 10;
        $now      = 3 * 86400 + 10;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $oldValues = [];

        $this->redis
            ->expects($this->exactly(count(SensorValuesGateway::FRAMES)))
            ->method('zrangebyscore')
            ->willReturn($oldValues);

        $this->redis
            ->expects($this->never())
            ->method('zrem');

        $actual = $this->subject->deleteOldValues($sensorId);

        $this->assertEquals(0, $actual);
    }
}
