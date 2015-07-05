<?php

namespace Tests\Homie\Webcam\WebcamListener;

use Homie\Webcam\Recorder;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\WebcamEvent;
use Homie\Webcam\WebcamListener;

/**
 * @covers Homie\Webcam\WebcamListener
 */
class WebcamListenerTest extends TestCase
{

    /**
     * @var WebcamListener
     */
    private $subject;

    /**
     * @var Recorder|MockObject
     */
    private $recorder;

    public function setUp()
    {
        $this->recorder = $this->getMock(Recorder::class, [], [], '', false);
        $this->subject  = new WebcamListener($this->recorder);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleWebcamEvent()
    {
        $name = 'shoot 123';
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->recorder
            ->expects($this->once())
            ->method('takePhoto')
            ->with($name);

        $this->subject->handlePictureEvent($event);
    }
}
