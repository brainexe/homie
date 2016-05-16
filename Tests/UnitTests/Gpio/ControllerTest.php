<?php

namespace Tests\Homie\Gpio;

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

    public function setUp()
    {
        $this->manager = $this->getMock(GpioManager::class, [], [], '', false);

        $this->subject = new Controller($this->manager);
    }

    public function testIndex()
    {
        $nodeId = 10;

        $pin  = new Pin();
        $pins = new PinsCollection('Type');
        $pins->add($pin);

        $request = new Request();

        $this->manager
            ->expects($this->once())
            ->method('getPins')
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

        $this->manager
            ->expects($this->once())
            ->method('setPin')
            ->with($gpioId, $status, $value)
            ->willReturn($pin);

        $actual = $this->subject->setStatus($request, $nodeId, $gpioId, $status, $value);

        $this->assertEquals($pin, $actual);
    }

    public function testSetDescription()
    {
        $nodeId      = 10;
        $pinId       = 100;
        $description = 'test';

        $request = new Request();
        $request->request->set('pinId', $pinId);
        $request->request->set('nodeId', $nodeId);
        $request->request->set('description', $description);

        $this->manager
            ->expects($this->once())
            ->method('setDescription')
            ->with($pinId, $description);

        $actualResult = $this->subject->setDescription($request);

        $this->assertTrue($actualResult);
    }
}
