<?php

namespace Tests\Homie\Webcam;

use Homie\Webcam\Recorder;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\WebcamEvent;
use Homie\Webcam\WebcamListener;

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

    public function testHandleWebcamEvent()
    {
        $name  = 'shoot 123';
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->recorder
            ->expects($this->once())
            ->method('takePhoto')
            ->with($name);

        $this->subject->handlePictureEvent($event);
    }

    public function testHandleVideoEvent()
    {
        $name  = 'shoot 123';
        $event = new WebcamEvent($name, WebcamEvent::TAKE_VIDEO, 5);

        $this->recorder
            ->expects($this->once())
            ->method('takeVideo')
            ->with($name, 5);

        $this->subject->handleVideoEvent($event);
    }
    public function testHandleSoundEvent()
    {
        $name  = 'shoot 123';
        $event = new WebcamEvent($name, WebcamEvent::TAKE_SOUND, 5);

        $this->recorder
            ->expects($this->once())
            ->method('takeSound')
            ->with($name, 5);

        $this->subject->handleSoundEvent($event);
    }
}
