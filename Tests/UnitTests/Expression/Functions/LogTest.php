<?php

namespace Tests\Homie\Expression\Functions;

use Homie\Expression\Functions\Log;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers \Homie\Expression\Functions\Log
 */
class LogTest extends TestCase
{

    /**
     * @var Log
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(Logger::class);

        $this->subject = new Log();
        $this->subject->setLogger($this->logger);
    }

    public function testEvaluatorDispatch()
    {
        $message = 'message';
        $context = 'context';
        $level = Logger::CRITICAL;

        $this->logger
            ->expects($this->once())
            ->method('log')
            ->with($level, $message, ['channel' => $context]);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $level, $message, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testCompilerDispatch()
    {
        /** @var ExpressionFunction $function */
        $actual   = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];

        $compiler = $function->getCompiler();
        $compiler();
    }
}
