<?php

namespace Tests\Raspberry\Radio;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJobListener;
use Raspberry\Radio\RadioController;
use Raspberry\Radio\VO\RadioVO;

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
        $radio->code = $code = 'code';
        $radio->pin = $pin = 'pin';

        $event = new RadioChangeEvent($radio, RadioChangeEvent::CHANGE_RADIO);
        $event->status = $status = 'status';

        $this->controller
            ->expects($this->once())
            ->method('setStatus')
            ->with($code, $pin, $status);

        $this->subject->handleChangeEvent($event);
    }
}
