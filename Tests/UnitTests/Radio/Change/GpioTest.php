<?php

namespace Tests\Homie\Radio\Change;

use Homie\Radio\Change\Gpio;
use Homie\Radio\VO\GpioSwitchVO;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Radio\Change\Gpio
 */
class GpioTest extends TestCase
{

    /**
     * @var Gpio
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Gpio();
    }

    public function testSetStatus()
    {
        $radioVo = new GpioSwitchVO();
        $radioVo->pin  = 2;
        $status = 1;

        $this->markTestIncomplete('gpio switch not final');
        $this->subject->setStatus($radioVo, $status);
    }
}
