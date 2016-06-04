<?php

namespace Tests\Homie\Switches\Change;

use Homie\Gpio\GpioManager;
use Homie\Node;
use Homie\Node\Gateway;
use Homie\Switches\Change\Gpio;
use Homie\Switches\VO\GpioSwitchVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Switches\Change\Gpio
 */
class GpioTest extends TestCase
{

    /**
     * @var Gpio
     */
    private $subject;

    /**
     * @var GpioManager|MockObject
     */
    private $manager;

    /**
     * @var Gateway|MockObject
     */
    private $nodes;

    public function setUp()
    {
        $this->manager = $this->createMock(GpioManager::class);
        $this->nodes   = $this->createMock(Gateway::class);
        $this->subject = new Gpio($this->manager, $this->nodes);
    }

    public function testSetStatus()
    {
        $switchVo = new GpioSwitchVO();
        $switchVo->pin    = 2;
        $switchVo->nodeId = 10;
        $status = 1;

        $node = new Node(10, Node::TYPE_RASPBERRY);
        
        $this->nodes
            ->expects($this->once())
            ->method('get')
            ->with($switchVo->nodeId)
            ->willReturn($node);

        $this->manager
            ->expects($this->once())
            ->method('setPin')
            ->with($this->isInstanceOf(Node::class), 2, true, true);

        $this->subject->setStatus($switchVo, $status);
    }
}
