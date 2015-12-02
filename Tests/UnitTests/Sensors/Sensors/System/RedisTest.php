<?php

namespace Tests\Homie\Sensors\Sensors\System;

use BrainExe\Tests\RedisMockTrait;
use Homie\Sensors\Sensors\System\Redis;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Predis\Client;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

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

        $actual = $this->subject->getValue($parameter);

        $this->assertEquals(42000, $actual);
    }

    public function testIsSupported()
    {
        $output = new DummyOutput();

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

        $actual = $this->subject->isSupported('memory.total_memory', $output);

        $this->assertTrue($actual);
    }

    public function testIsNotSupported()
    {
        $output = new DummyOutput();

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

        $actual = $this->subject->isSupported('memory.invalid', $output);

        $this->assertFalse($actual);
    }

    public function testSerialize()
    {
        $actual = json_encode($this->subject->jsonSerialize());

        $this->assertEquals('{"name":"Redis","type":"none","formatter":"none","neededPackages":null}', $actual);
    }
}