<?php

namespace Tests\Homie\Gpio;

use Homie\Gpio\Adapter;
use Homie\Gpio\Adapter\Factory;
use Homie\Node;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\GpioManager;
use Homie\Gpio\Pin;
use Homie\Gpio\PinGateway;
use Homie\Gpio\PinsCollection;

class GpioManagerTest extends TestCase
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
     * @var Factory|MockObject
     */
    private $adapterFactory;

    public function setUp()
    {
        $this->pinGateway     = $this->createMock(PinGateway::class);
        $this->adapterFactory = $this->createMock(Factory::class);

        $this->subject = new GpioManager($this->pinGateway, $this->adapterFactory);
    }

    public function testGetPins()
    {
        $pinId = 12;

        $node = new Node(1, Node::TYPE_ARDUINO);

        $descriptions = [
            $pinId => $description = 'description'
        ];

        $pin = new Pin();
        $pin->setPhysicalId($pinId);

        $collection = new PinsCollection();
        $collection->add($pin);

        $adapter = $this->createMock(Adapter::class);

        $this->adapterFactory
            ->expects($this->once())
            ->method('getForNode')
            ->with($node)
            ->willReturn($adapter);

        $adapter
            ->expects($this->once())
            ->method('loadPins')
            ->willReturn($collection);

        $this->pinGateway
            ->expects($this->once())
            ->method('getPinDescriptions')
            ->willReturn($descriptions);

        $actualResult = $this->subject->getPins($node);

        $this->assertEquals($collection, $actualResult);
        $this->assertEquals($description, $pin->getDescription());
    }

    public function testSetPin()
    {
        $gpioId = 10;
        $status = true;
        $value  = true;

        $node = new Node(1, 'type');

        $pin = new Pin();
        $pin->setPhysicalId($gpioId);

        $adapter = $this->createMock(Adapter::class);

        $this->adapterFactory
            ->expects($this->exactly(2))
            ->method('getForNode')
            ->with($node)
            ->willReturn($adapter);

        $adapter
            ->expects($this->once())
            ->method('loadPin')
            ->with($gpioId)
            ->willReturn($pin);
        $adapter
            ->expects($this->once())
            ->method('updatePin')
            ->with($pin);

        $actual = $this->subject->setPin($node, $gpioId, $status, $value);

        $this->assertEquals($pin, $actual);
    }

    public function testSetDescription()
    {
        $pinId       = 100;
        $description = 'test';
        $node        = new Node(1, Node::TYPE_ARDUINO);

        $this->pinGateway
            ->expects($this->once())
            ->method('setDescription')
            ->with($pinId, $description);

        $this->subject->setDescription($node, $pinId, $description);
    }
}
