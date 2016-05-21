<?php

namespace Tests\Homie\Gpio\Adapter;

use Exception;
use Homie\Gpio\Adapter\Arduino;
use Homie\Gpio\Adapter\Factory;
use Homie\Gpio\Adapter\Raspberry;
use Homie\Node;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\Pin;
use Homie\Gpio\PinLoader;
use Homie\Client\Adapter\LocalClient;
use Homie\Gpio\PinsCollection;

/**
 * @covers Homie\Gpio\Adapter\Factory
 */
class FactoryTest extends TestCase
{

    /**
     * @var Factory
     */
    private $subject;

    /**
     * @var Raspberry|MockObject
     */
    private $raspberry;

    /**
     * @var Arduino|MockObject
     */
    private $arduino;

    public function setUp()
    {
        $this->raspberry = $this->getMockWithoutInvokingTheOriginalConstructor(Raspberry::class);
        $this->arduino   = $this->getMockWithoutInvokingTheOriginalConstructor(Arduino::class);

        $this->subject = new Factory(
            $this->raspberry,
            $this->arduino
        );
    }

    public function testGetRaspberry()
    {
        $node = new Node(1, Node::TYPE_RASPBERRY);

        $actual = $this->subject->getForNode($node);

        $this->assertEquals($this->raspberry, $actual);
    }

    public function testGetArduino()
    {
        $node = new Node(1, Node::TYPE_ARDUINO);

        $actual = $this->subject->getForNode($node);

        $this->assertEquals($this->arduino, $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid type: type
     */
    public function testGetUndefined()
    {
        $node = new Node(1, 'type');

        $this->subject->getForNode($node);
    }
}
