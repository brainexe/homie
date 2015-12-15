<?php

namespace Tests\Homie\Radio;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\SwitchChangeEvent;
use Homie\Radio\JobListener;
use Homie\Radio\RadioController;
use Homie\Radio\VO\RadioVO;

class JobListenerTest extends TestCase
{

    /**
     * @var JobListener
     */
    private $subject;

    /**
     * @var RadioController|MockObject
     */
    private $controller;

    public function setUp()
    {
        $this->controller = $this->getMock(RadioController::class, [], [], '', false);

        $this->subject = new JobListener($this->controller);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleChangeEvent()
    {
        $radio = new RadioVO();
        $radio->code = 'code';
        $radio->pin  = 'pin';

        $event = new SwitchChangeEvent($radio, SwitchChangeEvent::CHANGE_RADIO);
        $event->status = $status = 'status';

        $this->controller
            ->expects($this->once())
            ->method('setStatus')
            ->with($radio, $status);

        $this->subject->handleChangeEvent($event);
    }
}
