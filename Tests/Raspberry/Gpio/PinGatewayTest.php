<?php

namespace Tests\Raspberry\Gpio\PinGateway;


use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Gpio\PinGateway;

/**
 * @Covers Raspberry\Gpio\PinGateway
 */
class PinGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var PinGateway
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();

        $this->subject = new PinGateway();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetPinDescriptions()
    {
        $descriptions = ['descriptions'];

        $this->mockRedis
            ->expects($this->once())
            ->method('hGetAll')
            ->with(PinGateway::REDIS_PINS)
            ->willReturn($descriptions);

        $actualResult = $this->subject->getPinDescriptions();

        $this->assertEquals($descriptions, $actualResult);
    }
}
