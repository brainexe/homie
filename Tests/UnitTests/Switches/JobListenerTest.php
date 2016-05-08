<?php

namespace Tests\Homie\Switches;

use Homie\Switches\Change\Change;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Switches\SwitchChangeEvent;
use Homie\Switches\JobListener;
use Homie\Switches\VO\RadioVO;

class JobListenerTest extends TestCase
{

    /**
     * @var JobListener
     */
    private $subject;

    /**
     * @var Change|MockObject
     */
    private $change;

    public function setUp()
    {
        $this->change = $this->getMockWithoutInvokingTheOriginalConstructor(Change::class);

        $this->subject = new JobListener($this->change);
    }

    public function testHandleChangeEvent()
    {
        $radio = new RadioVO();
        $radio->code = 'code';
        $radio->pin  = 'pin';
        $status      = true;

        $event = new SwitchChangeEvent($radio, $status);

        $this->change
            ->expects($this->once())
            ->method('setStatus')
            ->with($radio, $status);

        $this->subject->handleChangeEvent($event);
    }
}
