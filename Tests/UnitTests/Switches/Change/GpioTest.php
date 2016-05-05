<?php

namespace Tests\Homie\Switches\Change;

use Homie\Gpio\GpioManager;
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

    public function setUp()
    {
        $this->manager = $this->getMock(GpioManager::class, [], [], '', false);
        $this->subject = new Gpio($this->manager);
    }

    public function testSetStatus()
    {
        $switchVo = new GpioSwitchVO();
        $switchVo->pin  = 2;
        $status = 1;

        $this->manager
            ->expects($this->once())
            ->method('setPin')
            ->with(2, true, true);

        $this->subject->setStatus($switchVo, $status);
    }
}
