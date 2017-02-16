<?php

namespace Homie\Tests\Switches;

use Homie\Switches\SwitchChangeEvent;
use Homie\Switches\VO\GpioSwitchVO;
use PHPUnit\Framework\TestCase;

class SwitchChangeEventTest extends TestCase
{

    public function testEvent()
    {
        $switch = new GpioSwitchVO();
        $status = 1;

        $subject = new SwitchChangeEvent($switch, $status);

        $this->assertEquals($switch, $subject->getSwitch());
        $this->assertEquals($status, $subject->getStatus());
        $this->assertEquals(SwitchChangeEvent::CHANGE, $subject->getEventName());
    }
}
