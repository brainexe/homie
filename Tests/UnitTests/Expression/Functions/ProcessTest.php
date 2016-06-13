<?php

namespace Tests\Homie\Expression\Functions;

use Homie\Client\Adapter\LocalClient;
use Homie\Expression\Functions\Process;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Expression\Functions\Process
 */
class ProcessTest extends TestCase
{

    /**
     * @var Process
     */
    private $subject;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->createMock(LocalClient::class);

        $this->subject = new Process($this->client);
    }

    public function testEvaluator()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->with('command', ['args'])
            ->willReturn('result');

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $actual = $evaluator([], 'command', ['args']);

        $this->assertEquals('result', $actual);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "executeCommand" is not allowed as trigger
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
