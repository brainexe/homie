<?php

namespace Tests\Homie\InputControl;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Homie\Remote\Event\ReceivedEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Homie\Remote\ExpressionLanguage;

/**
 * @covers \Homie\Remote\ExpressionLanguage
 */
class ExpressionLanguageTest extends TestCase
{

    /**
     * @var ExpressionLanguage
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject    = new ExpressionLanguage();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testEvaluatorMatching()
    {
        $code = 'myCode';
        $event = new ReceivedEvent('myCode');

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator(['eventName' => ReceivedEvent::RECEIVED, 'event' => $event], $code);

        $this->assertTrue($actual);
    }

    public function testEvaluatorOtherEvent()
    {
        $code = 'myCode';
        $event = new TimingEvent('sdsd');

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator(['eventName' => TimingEvent::TIMING_EVENT, 'event' => $event], $code);

        $this->assertFalse($actual);
    }

    public function testCompiler()
    {
        $code = 'code';

        /** @var ExpressionFunction $function */
        $actual   = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiled = $compiler($code);

        $this->assertEquals('($eventName === \'remote.received\' && $event->getCode() === code)', $compiled);
    }
}
