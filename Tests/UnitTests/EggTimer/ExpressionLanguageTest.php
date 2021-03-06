<?php

namespace Tests\Homie\EggTimer;

use Generator;
use Homie\EggTimer\EggTimer;
use Homie\EggTimer\ExpressionLanguage;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers \Homie\EggTimer\ExpressionLanguage
 */
class ExpressionLanguageTest extends TestCase
{

    /**
     * @var ExpressionLanguage
     */
    private $subject;

    /**
     * @var EggTimer|MockObject
     */
    private $eggTimer;

    public function setUp()
    {
        $this->eggTimer = $this->createMock(EggTimer::class);
        $this->subject  = new ExpressionLanguage($this->eggTimer);
    }

    public function testGetFunctions()
    {
        $actual = $this->subject->getFunctions();
        $this->assertInstanceOf(Generator::class, $actual);
    }

    public function testSetTimer()
    {
        $time = '1h';
        $text = 'my text';

        $this->eggTimer
            ->expects($this->once())
            ->method('addNewJob')
            ->with($time, $text);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $time, $text);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "eggTimer" is not allowed as trigger
     */
    public function testSetTimerCompiler()
    {
        $time = '1h';
        $text = 'my text';

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $compiler = $function->getCompiler();
        $compiler($time, $text);
    }
}
