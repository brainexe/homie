<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\Time;
use BrainExe\Tests\RedisMockTrait;
use Homie\Sensors\DeleteOldValues;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers Homie\Sensors\DeleteOldValues
 */
class DeleteOldValuesTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var DeleteOldValues
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
        $this->time  = $this->createMock(Time::class);

        $this->subject = new DeleteOldValues();
        $this->subject->setRedis($this->redis);
        $this->subject->setTime($this->time);
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
            ->expects($this->exactly(count(DeleteOldValues::FRAMES)))
            ->method('zrangebyscore')
            ->willReturn($oldValues);

        $this->redis
            ->expects($this->exactly(6))
            ->method('zrem');

        $actual = $this->subject->deleteValues($sensorId);

        $this->assertEquals(6, $actual);
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
            ->expects($this->exactly(count(DeleteOldValues::FRAMES)))
            ->method('zrangebyscore')
            ->willReturn($oldValues);

        $this->redis
            ->expects($this->never())
            ->method('zrem');

        $actual = $this->subject->deleteValues($sensorId);

        $this->assertEquals(0, $actual);
    }
}
