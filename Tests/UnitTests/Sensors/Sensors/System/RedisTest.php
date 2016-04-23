<?php

namespace Tests\Homie\Sensors\Sensors\System;

use BrainExe\Tests\RedisMockTrait;
use Homie\Sensors\Sensors\System\Redis;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Predis\Client;

class RedisTest extends TestCase
{
    use RedisMockTrait;

    /**
     * @var Redis
     */
    private $subject;

    /**
     * @var Client|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new Redis();
        $this->subject->setRedis($this->redis);
    }

    public function testGetSensorType()
    {
        $actualResult = $this->subject->getSensorType();

        $this->assertEquals(Redis::TYPE, $actualResult);
    }

    public function testGetValue()
    {
        $parameter = 'memory.used_memory';
        $sensor = new SensorVO();
        $sensor->parameter = $parameter;

        $this->redis
            ->expects($this->once())
            ->method('info')
            ->with('Memory')
            ->willReturn([
                'Memory' => [
                    'total_memory' => 100,
                    'used_memory' => 42000
                ]
            ]);

        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(42000, $actual);
    }

    public function testIsSupported()
    {
        $this->redis
            ->expects($this->once())
            ->method('info')
            ->with('Memory')
            ->willReturn([
                'Memory' => [
                    'total_memory' => 100,
                    'used_memory' => 42000
                ]
            ]);

        $sensor = new SensorVO();
        $sensor->parameter = $parameter = 'memory.total_memory';

        $actual = $this->subject->isSupported($sensor);

        $this->assertTrue($actual);
    }

    /**
     * @expectedException \Homie\Sensors\Exception\InvalidSensorValueException
     * @expectedExceptionMessage Invalid stats key: "memory.invalid"
     */
    public function testIsNotSupported()
    {
        $this->redis
            ->expects($this->once())
            ->method('info')
            ->with('Memory')
            ->willReturn([
                'Memory' => [
                    'total_memory' => 100,
                    'used_memory' => 42000
                ]
            ]);
        $sensor = new SensorVO();
        $sensor->parameter = 'memory.invalid';

        $this->subject->isSupported($sensor);
    }

    public function testSerialize()
    {
        $actual = json_encode($this->subject->jsonSerialize());
        $this->assertInternalType('string', $actual);
    }
}
