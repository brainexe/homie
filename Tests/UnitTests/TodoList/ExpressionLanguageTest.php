<?php

namespace Tests\Homie\TodoList;

use Homie\TodoList\ExpressionLanguage;
use Homie\TodoList\TodoReminder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\TodoList\ExpressionLanguage
 */
class ExpressionLanguageTest extends TestCase
{

    /**
     * @var ExpressionLanguage
     */
    private $subject;

    /**
     * @var TodoReminder|MockObject
     */
    private $reminder;

    public function setUp()
    {
        $this->reminder = $this->getMock(TodoReminder::class, [], [], '', false);
        $this->subject  = new ExpressionLanguage($this->reminder);
    }

    public function testGetFunctions()
    {
        $this->reminder
            ->expects($this->once())
            ->method('sendNotification');

        $actual = iterator_to_array($this->subject->getFunctions());

        /** @var callable $function */
        $function = $actual[0]->getEvaluator();
        $function([]);

        $this->assertInternalType('array', $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCompiler()
    {
        /** @var callable $compiler */
        $actual = iterator_to_array($this->subject->getFunctions());
        $compiler = $actual[0]->getCompiler();

        $compiler([]);
    }
}
