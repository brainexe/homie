<?php

namespace Tests\Homie\Gpio;

use Homie\Node;
use Homie\Node\Gateway;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\Controller;
use Homie\Gpio\Pin;
use Homie\Gpio\PinsCollection;
use Symfony\Component\HttpFoundation\Request;
use Homie\Gpio\GpioManager;

/**
 * @covers Homie\Gpio\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var GpioManager|MockObject
     */
    private $manager;

    /**
     * @var Gateway|MockObject
     */
    private $nodeGateway;

    public function setUp()
    {
        $this->manager     = $this->getMock(GpioManager::class, [], [], '', false);
        $this->nodeGateway = $this->getMock(Gateway::class, [], [], '', false);

        $this->subject = new Controller(
            $this->manager,
            $this->nodeGateway
        );
    }

    public function testIndex()
    {
        $nodeId = 10;

        $pin  = new Pin();
        $pins = new PinsCollection('Type');
        $pins->add($pin);

        $node = new Node($nodeId, Node::TYPE_ARDUINO);

        $request = new Request();

        $this->nodeGateway
            ->expects($this->once())
            ->method('get')
            ->with($nodeId)
            ->willReturn($node);

        $this->manager
            ->expects($this->once())
            ->method('getPins')
            ->with($node)
            ->willReturn($pins);

        $actual = $this->subject->index($request, $nodeId);
        $expected = [
            'pins' => array_values($pins->getAll()),
            'type' => 'Type'
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSetStatus()
    {
        $request = new Request();
        $nodeId  = 1;
        $gpioId  = 10;
        $status  = true;
        $value   = false;
        $pin     = new Pin();

        $node = new Node($nodeId, Node::TYPE_ARDUINO);

        $this->nodeGateway
            ->expects($this->once())
            ->method('get')
            ->with($nodeId)
            ->willReturn($node);

        $this->manager
            ->expects($this->once())
            ->method('setPin')
            ->with($node, $gpioId, $status, $value)
            ->willReturn($pin);

        $actual = $this->subject->setStatus($request, $nodeId, $gpioId, $status, $value);

        $this->assertEquals($pin, $actual);
    }

    public function testSetDescription()
    {
        $nodeId      = 10;
        $pinId       = 100;
        $description = 'test';

        $node = new Node($nodeId, Node::TYPE_ARDUINO);

        $this->nodeGateway
            ->expects($this->once())
            ->method('get')
            ->with($nodeId)
            ->willReturn($node);

        $request = new Request();
        $request->request->set('pinId', $pinId);
        $request->request->set('nodeId', $nodeId);
        $request->request->set('description', $description);

        $this->manager
            ->expects($this->once())
            ->method('setDescription')
            ->with($node, $pinId, $description);

        $actualResult = $this->subject->setDescription($request);

        $this->assertTrue($actualResult);
    }
}
