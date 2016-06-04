<?php

namespace Tests\Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Homie\Expression\Functions\Events;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Expression\Functions\Events
 */
class EventsTest extends TestCase
{

    /**
     * @var Events
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->subject    = new Events();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testEvaluatorIsEvent()
    {
        $eventName = 'eventName';

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator(['eventName' => $eventName], $eventName);

        $this->assertTrue($actual);
    }

    public function testEvaluatorNotMatching()
    {
        $eventName = 'eventName';

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator(['eventName' => $eventName], $eventName);

        $this->assertTrue($actual);
    }

    public function testCompilerIsEvent()
    {
        $eventName = '"eventName"';

        /** @var ExpressionFunction $function */
        $actual   = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $actual = $compiler($eventName);

        $this->assertEquals('($eventName == "eventName")', $actual);
    }

    public function testEvaluatorDispatch()
    {
        $eventName = TimingEvent::TIMING_EVENT;
        $timingId  = '@daily';

        $event = new TimingEvent($timingId);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[1];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $eventName, $timingId);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "event" is not allowed as trigger
     */
    public function testCompilerDispatch()
    {
        $eventName = 'eventName';

        /** @var ExpressionFunction $function */
        $actual   = iterator_to_array($this->subject->getFunctions());
        $function = $actual[1];

        $compiler = $function->getCompiler();
        $compiler($eventName);
    }
}
