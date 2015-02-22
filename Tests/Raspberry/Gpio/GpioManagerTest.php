<?php

namespace Tests\Raspberry\Gpio\GpioManager;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Gpio\GpioManager;
use Raspberry\Gpio\Pin;
use Raspberry\Gpio\PinGateway;
use Raspberry\Client\LocalClient;
use Raspberry\Gpio\PinLoader;
use Raspberry\Gpio\PinsCollection;

class GpioManagerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var GpioManager
     */
    private $subject;

    /**
     * @var PinGateway|MockObject
     */
    private $pinGateway;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var PinLoader|MockObject
     */
    private $pinLoader;

    public function setUp()
    {
        $this->pinGateway = $this->getMock(PinGateway::class, [], [], '', false);
        $this->client = $this->getMock(LocalClient::class, [], [], '', false);
        $this->pinLoader = $this->getMock(PinLoader::class, [], [], '', false);

        $this->subject = new GpioManager($this->pinGateway, $this->client, $this->pinLoader);
    }

    public function testGetPins()
    {
        $pinId = 12;

        $descriptions = [
            $pinId => $description = 'description'
        ];

        $pin = new Pin();
        $pin->setID($pinId);

        $collection = new PinsCollection();
        $collection->add($pin);

        $this->pinLoader
            ->expects($this->once())
            ->method('loadPins')
            ->willReturn($collection);

        $this->pinGateway
            ->expects($this->once())
            ->method('getPinDescriptions')
            ->willReturn($descriptions);

        $actualResult = $this->subject->getPins();

        $this->assertEquals($collection, $actualResult);
        $this->assertEquals($description, $pin->getDescription());
    }

    public function testSetPin()
    {
        $gpioId = 10;
        $status = true;
        $value  = true;

        $pin = new Pin();
        $pin->setID($gpioId);

        $this->pinLoader
            ->expects($this->once())
            ->method('loadPin')
            ->with($gpioId)
            ->willReturn($pin);

        $this->client
            ->expects($this->at(0))
            ->method('execute')
            ->with(sprintf(GpioManager::GPIO_COMMAND_DIRECTION, $gpioId, 'out'));

        $this->client
            ->expects($this->at(1))
            ->method('execute')
            ->with(sprintf(GpioManager::GPIO_COMMAND_VALUE, $gpioId, 1));

        $actualResult = $this->subject->setPin($gpioId, $status, $value);

        $this->assertEquals($pin, $actualResult);
    }

    public function testSetDescription()
    {
        $pinId       = 100;
        $description = 'test';

        $this->pinGateway
            ->expects($this->once())
            ->method('setDescription')
            ->with($pinId, $description);

        $this->subject->setDescription($pinId, $description);
    }
}
