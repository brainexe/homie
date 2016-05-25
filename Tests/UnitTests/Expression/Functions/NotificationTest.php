<?php

namespace Tests\Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Notification\Notification as NotificationEvent;
use Homie\Expression\Functions\Notification;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Expression\Functions\Notification
 */
class NotificationTest extends TestCase
{

    /**
     * @var Notification
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject    = new Notification();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testNotify()
    {
        $message = 'message';
        $subject = 'subject';
        $level = 'level';

        $mailEvent = new NotificationEvent($message, $subject, $level);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($mailEvent);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $message, $subject, $level);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "addNotification" is not allowed as trigger
     */
    public function testNotifyCompiler()
    {
        /** @var ExpressionFunction $function */
        $actual   = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiler();
    }
}
