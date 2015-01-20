<?php

namespace Tests\Raspberry\Radio\RadioJobListener;

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
    private $mockRadioController;

    public function setUp()
    {
        $this->mockRadioController = $this->getMock(RadioController::class, [], [], '', false);

        $this->subject = new RadioJobListener($this->mockRadioController);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleChangeEvent()
    {
        $radio_vo = new RadioVO();
        $radio_vo->code = $code = 'code';
        $radio_vo->pin = $pin = 'pin';

        $event = new RadioChangeEvent($radio_vo, RadioChangeEvent::CHANGE_RADIO);
        $event->status = $status = 'status';

        $this->mockRadioController
            ->expects($this->once())
            ->method('setStatus')
            ->with($code, $pin, $status);

        $this->subject->handleChangeEvent($event);
    }
}
