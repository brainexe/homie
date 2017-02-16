<?php

namespace Tests\Homie\Sensors\Sensors\System;

use BrainExe\Tests\RedisMockTrait;
use Homie\Sensors\Sensors\System\Redis;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
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
     * @expectedExceptionMessage Not supported section: memory.invalid
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

    public function testSearch()
    {
        $data = [
            'CPU' => [
                'used_cpu_sys' => '3.40',
                'used_cpu_user' => '2.26',
                'used_cpu_sys_children' => '0.07',
                'used_cpu_user_children' => '0.14',
            ],
            'Cluster' => [
                'cluster_enabled' => '0',
            ],
            'Keyspace' => [
                'db0' => [
                    'keys' => '262',
                    'expires' => '81',
                    'avg_ttl' => '7998256548',
                ],
                'db3' => [
                    'keys' => '519',
                    'expires' => '0',
                    'avg_ttl' => '0',
                ],
            ]
        ];
        $this->redis->expects($this->once())
            ->method('info')
            ->willReturn($data);

        $actual = $this->subject->search();

        $expected = [
            'CPU.used_cpu_sys',
            'CPU.used_cpu_user',
            'CPU.used_cpu_sys_children',
            'CPU.used_cpu_user_children',
            'Cluster.cluster_enabled',
        ];

        $this->assertEquals($expected, $actual);
    }
}
