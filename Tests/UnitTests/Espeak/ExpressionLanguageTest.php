<?php

namespace Tests\Homie\Espeak;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Generator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\Espeak\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers \Homie\Espeak\ExpressionLanguage
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

        $this->subject = new ExpressionLanguage();
        $this->subject->setEventDispatcher($this->dispatcher);
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
     * @expectedException \Exception
     * @expectedExceptionMessage Function "say" is not allowed as trigger
     */
    public function testSetTimerCompiler()
    {
        $text = 'my text';

        /** @var Generator|ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $compiler = $function->getCompiler();
        $compiler($text, 100, 100);
    }
}
