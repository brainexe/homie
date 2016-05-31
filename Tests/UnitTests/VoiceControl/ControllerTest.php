<?php

namespace Tests\Homie\VoiceControl;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\VoiceControl\Controller;
use Homie\VoiceControl\VoiceEvent;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\VoiceControl\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher  = $this->getMockWithoutInvokingTheOriginalConstructor(EventDispatcher::class);

        $this->subject = new Controller();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSpeech()
    {
        $text = 'myText';

        $request = new Request();
        $request->request->set('text', $text);

        $event = new VoiceEvent($text);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $actual = $this->subject->text($request);

        $this->assertTrue($actual);
    }
}
