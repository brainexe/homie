<?php

namespace Tests\Homie\Gpio;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\PinGateway;

/**
 * @covers Homie\Gpio\PinGateway
 */
class PinGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var PinGateway
     */
    private $subject;

    /**
     * @var MockObject|Predis
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new PinGateway();
        $this->subject->setRedis($this->redis);
    }

    public function testGetPinDescriptions()
    {
        $descriptions = ['descriptions'];

        $this->redis
            ->expects($this->once())
            ->method('hGetAll')
            ->with(PinGateway::REDIS_PINS)
            ->willReturn($descriptions);

        $actualResult = $this->subject->getPinDescriptions();

        $this->assertEquals($descriptions, $actualResult);
    }
    public function testSetDescription()
    {
        $pinId       = 100;
        $description = 'test';

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(PinGateway::REDIS_PINS, $pinId, $description);

        $this->subject->setDescription($pinId, $description);
    }
}
