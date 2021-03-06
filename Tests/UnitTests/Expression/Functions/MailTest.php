<?php

namespace Tests\Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Mail\SendMailEvent;
use Homie\Expression\Functions\Mail;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers \Homie\Expression\Functions\Mail
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
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->subject    = new Mail();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSendMailExpression()
    {
        $recipient = 'myRecipient';
        $subject = 'mySubject';
        $body = 'myBody';

        $mailEvent = new SendMailEvent($recipient, $subject, $body);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($mailEvent);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $recipient, $subject, $body);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "sendMail" is not allowed as trigger
     */
    public function testSendMailCompiler()
    {
        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiler();
    }
}
