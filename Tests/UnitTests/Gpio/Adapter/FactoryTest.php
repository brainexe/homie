<?php

namespace Tests\Homie\Gpio\Adapter;


use Homie\Gpio\Adapter\Arduino;
use Homie\Gpio\Adapter\Factory;
use Homie\Gpio\Adapter\Raspberry;
use Homie\Node;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Homie\Gpio\PinLoader;

/**
 * @covers \Homie\Gpio\Adapter\Factory
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
        $this->raspberry = $this->createMock(Raspberry::class);
        $this->arduino   = $this->createMock(Arduino::class);

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
