<?php

namespace Tests\Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\ConsoleEvent;
use Homie\Expression\Functions\Console;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers \Homie\Expression\Functions\Console
 */
class ConsoleTest extends TestCase
{

    /**
     * @var Console
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Console();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testEvaluator()
    {
        $command = "myCommand";

        $event = new ConsoleEvent($command);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $command);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "console" is not allowed as trigger
     */
    public function testCompiler()
    {
        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiler();
    }
}
