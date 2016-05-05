<?php

namespace Tests\Homie\Gpio;

use Exception;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\Pin;
use Homie\Gpio\PinLoader;
use Homie\Client\Adapter\LocalClient;
use Homie\Gpio\PinsCollection;

/**
 * @covers Homie\Gpio\PinLoader
 */
class PinLoaderTest extends TestCase
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
        $this->subject = new PinLoader($this->client, './gpio');
    }

    public function testGetPins()
    {
        $gpioResult = " +-----+-----+---------+------+---+---Unknown+---+------+---------+-----+-----+
 | BCM | wPi |   Name  | Mode | V | Physical | V | Mode | Name    | wPi | BCM |
 +-----+-----+---------+------+---+----++----+---+------+---------+-----+-----+
 |  17 |   1 | GPIO. 0 |   IN | 0 | 11 || 12 | 0 | IN   | GPIO. 1 | 2   | 18  |
 +-----+-----+---------+------+---+----++----+---+------+---------+-----+-----+
 | BCM | wPi |   Name  | Mode | V | Physical | V | Mode | Name    | wPi | BCM |
 +-----+-----+---------+------+---+---Pi 2---+---+------+---------+-----+-----+\n";

        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with('./gpio readall')
            ->willReturn($gpioResult);

        $actual = $this->subject->loadPins();

        $expected = new PinsCollection('Unknown');

        $pin = new Pin();
        $pin->setPhysicalId(11);
        $pin->setWiringId(1);
        $pin->setName('GPIO. 0');
        $pin->setMode('IN');
        $pin->setValue(false);
        $expected->add($pin);

        $pin = new Pin();
        $pin->setPhysicalId(12);
        $pin->setWiringId(2);
        $pin->setName('GPIO. 1');
        $pin->setMode('IN');
        $pin->setValue(false);
        $expected->add($pin);

        $this->assertEquals($expected, $actual);
        $this->assertEquals('IN', $pin->getMode());
        $this->assertEquals(0, $pin->getValue());

        $actual = $this->subject->loadPins();
        $this->assertEquals($expected, $actual);

        $this->assertEquals($pin, $this->subject->loadPin(2));
    }

    public function testGetPinsFallback()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with('./gpio readall')
            ->willThrowException(new Exception());

        $actual = $this->subject->loadPins();

        $this->assertCount(40, $actual->getAll());
    }
}
