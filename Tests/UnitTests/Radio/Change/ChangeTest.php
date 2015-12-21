<?php

namespace Tests\Homie\Radio\Change;

use Homie\Radio\Change\Change;
use Homie\Radio\Change\Gpio;
use Homie\Radio\Change\Radio;
use Homie\Radio\VO\GpioSwitchVO;
use Homie\Radio\VO\RadioVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Radio\Change\Change
 */
class ChangeTest extends TestCase
{

    /**
     * @var Change
     */
    private $subject;

    /**
     * @var Gpio|MockObject
     */
    private $gpio;

    /**
     * @var Radio|MockObject
     */
    private $radio;

    public function setUp()
    {
        $this->radio = $this->getMock(Radio::class, [], [], '', false);
        $this->gpio  = $this->getMock(Gpio::class, [], [], '', false);
        $this->subject = new Change(
            $this->radio,
            $this->gpio
        );
    }

    public function testGpioStatus()
    {
        $switch = new GpioSwitchVO();
        $switch->pin  = 2;

        $status = 1;

        $this->gpio
            ->expects($this->once())
            ->method('setStatus')
            ->with($switch, $status);

        $this->subject->setStatus($switch, $status);
    }

    public function testRadioStatus()
    {
        $switch = new RadioVO();
        $switch->pin  = 2;

        $status = 1;

        $this->radio
            ->expects($this->once())
            ->method('setStatus')
            ->with($switch, $status);

        $this->subject->setStatus($switch, $status);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid switch type: invalid
     */
    public function testInvalidType()
    {
        $switch = new GpioSwitchVO();
        $switch->type = 'invalid';

        $status = 1;

        $this->subject->setStatus($switch, $status);
    }
}
