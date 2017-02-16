<?php

namespace Tests\Homie\Switches\Change;

use Homie\Switches\Change\Change;
use Homie\Switches\Change\Gpio;
use Homie\Switches\Change\Radio;
use Homie\Switches\Gateway;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\RadioVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Switches\Change\Change
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

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->radio = $this->createMock(Radio::class);
        $this->gpio  = $this->createMock(Gpio::class);
        $this->gateway  = $this->createMock(Gateway::class);

        $this->subject = new Change(
            $this->radio,
            $this->gpio,
            $this->gateway
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

        $this->gateway
            ->expects($this->once())
            ->method('edit')
            ->with($switch);

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

        $this->gateway
            ->expects($this->once())
            ->method('edit')
            ->with($switch);

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

        $this->gateway
            ->expects($this->never())
            ->method('edit');

        $this->subject->setStatus($switch, $status);
    }
}
