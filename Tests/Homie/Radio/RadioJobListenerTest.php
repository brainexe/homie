<?php

namespace Tests\Homie\Radio;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\RadioChangeEvent;
use Homie\Radio\RadioJobListener;
use Homie\Radio\RadioController;
use Homie\Radio\VO\RadioVO;

class RadioJobListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var RadioJobListener
     */
    private $subject;

    /**
     * @var RadioController|MockObject
     */
    private $controller;

    public function setUp()
    {
        $this->controller = $this->getMock(RadioController::class, [], [], '', false);

        $this->subject = new RadioJobListener($this->controller);
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

        $event = new RadioChangeEvent($radio, RadioChangeEvent::CHANGE_RADIO);
        $event->status = $status = 'status';

        $this->controller
            ->expects($this->once())
            ->method('setStatus')
            ->with($radio, $status);

        $this->subject->handleChangeEvent($event);
    }
}
