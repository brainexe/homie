<?php

namespace Tests\Homie\TodoList;

use BrainExe\Core\Authentication\AnonymusUserVO;
use Homie\TodoList\ExpressionLanguage;
use Homie\TodoList\TodoList;
use Homie\TodoList\TodoReminder;
use Homie\TodoList\VO\TodoItemVO;
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

    /**
     * @var TodoList|MockObject
     */
    private $todoList;

    public function setUp()
    {
        $this->reminder = $this->getMockWithoutInvokingTheOriginalConstructor(TodoReminder::class);
        $this->todoList = $this->getMockWithoutInvokingTheOriginalConstructor(TodoList::class);

        $this->subject  = new ExpressionLanguage(
            $this->reminder,
            $this->todoList
        );
    }

    public function testSendNotificationFunctions()
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

    public function testAddItem()
    {
        $user = new AnonymusUserVO();
        $item = new TodoItemVO();
        $item->name = 'myItem';

        $actual = iterator_to_array($this->subject->getFunctions());

        $this->todoList
            ->expects($this->once())
            ->method('addItem')
            ->with($user, $item);

        /** @var callable $function */
        $function = $actual[1]->getEvaluator();
        $function([], 'myItem');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "sayTodoList" is not allowed as trigger
     */
    public function testSayCompiler()
    {
        /** @var callable $compiler */
        $actual = iterator_to_array($this->subject->getFunctions());
        $compiler = $actual[0]->getCompiler();
        $compiler();
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "addTodoTodoItem" is not allowed as trigger
     */
    public function testCompiler()
    {
        /** @var callable $compiler */
        $actual = iterator_to_array($this->subject->getFunctions());
        $compiler = $actual[1]->getCompiler();
        $compiler();
    }
}
