<?php

namespace Tests\Homie\InputControl;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Mail\SendMailEvent;
use BrainExe\InputControl\Event;
use Homie\InputControl\Mail;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers Homie\InputControl\Mail
 */
class MailTest extends TestCase
{

    /**
     * @var Mail
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject    = new Mail();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testPlaySound()
    {

        $event = new Event();
        $event->matches = [
            $recipient = 'myRecipient',
            $subject = 'mySubject',
            $body = 'myBody'
        ];

        $mailEvent = new SendMailEvent($recipient, $subject, $body);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($mailEvent);

        $this->subject->sendMail($event);
    }
}
