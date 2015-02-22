<?php

namespace Tests\Raspberry\Gpio\PinLoader;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\Pin;
use Raspberry\Gpio\PinLoader;
use Raspberry\Client\LocalClient;
use Raspberry\Gpio\PinsCollection;

/**
 * @Covers Raspberry\Gpio\PinLoader
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
    private $mockLocalClient;

    public function setUp()
    {
        $this->mockLocalClient = $this->getMock(LocalClient::class, [], [], '', false);

        $this->subject = new PinLoader($this->mockLocalClient);
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

        $this->mockLocalClient
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
}
