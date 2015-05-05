<?php

namespace Tests\Homie\Gpio;

use Exception;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\GpioManager;
use Homie\Gpio\Pin;
use Homie\Gpio\PinLoader;
use Homie\Client\LocalClient;
use Homie\Gpio\PinsCollection;

/**
 * @covers Homie\Gpio\PinLoader
 */
class PinLoaderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PinLoader
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client  = $this->getMock(LocalClient::class, [], [], '', false);
        $this->subject = new PinLoader($this->client);
    }

    public function testGetPins()
    {
        $pinId     = 12;
        $name      = 'name';
        $direction = 'IN';
        $value     = 'Low';

        $gpioResult = "+----------+-Rev2-+------+--------+------+-------+
| wiringPi | GPIO | Phys | Name   | Mode | Value |
+----------+------+------+--------+------+-------+
|      $pinId   |  17  |  11  | $name | $direction   | $value   |
+----------+------+------+--------+------+-------+\n";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with(GpioManager::GPIO_COMMAND_READALL)
            ->willReturn($gpioResult);

        $actualResult = $this->subject->loadPins();

        $expectedPin = new Pin();
        $expectedPin->setID($pinId);
        $expectedPin->setName($name);
        $expectedPin->setDirection($direction);
        $expectedPin->setValue(0);

        $expectedCollection = new PinsCollection();
        $expectedCollection->add($expectedPin);

        $this->assertEquals($expectedCollection, $actualResult);
        $this->assertEquals($direction, $expectedPin->getDirection());
        $this->assertEquals(0, $expectedPin->isHighValue());

        $actualResult = $this->subject->loadPins();
        $this->assertEquals($expectedCollection, $actualResult);

        $this->assertEquals($expectedPin, $this->subject->loadPin($pinId));
    }

    public function testGetPinsFallback()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with(GpioManager::GPIO_COMMAND_READALL)
            ->willThrowException(new Exception());

        $actualResult = $this->subject->loadPins();

        $this->assertCount(21, $actualResult->getAll());
    }
}
