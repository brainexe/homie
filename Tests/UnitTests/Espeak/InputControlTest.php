<?php

namespace Tests\Homie\Espeak;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use Generator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\Espeak\InputControl;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Espeak\InputControl
 */
class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new InputControl();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();

        $this->assertInternalType('array', $actualResult);
    }

    public function testSay()
    {
        $inputEvent = new Event();
        $inputEvent->matches = ['say', 'text'];

        $event = new EspeakEvent(new EspeakVO('text'));

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->say($inputEvent);
    }

    public function testSetTimer()
    {
        $text = 'text';

        $event = new EspeakEvent(new EspeakVO($text));

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);


        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $text);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage say() is not available in this context
     */
    public function testSetTimerCompiler()
    {
        $time = '1h';
        $text = 'my text';

        /** @var Generator|ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $compiler = $function->getCompiler();
        $compiler([], $time, $text);
    }
}
