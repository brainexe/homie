<?php

namespace Tests\Homie\TodoList;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\TodoList\Builder;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @covers Homie\TodoList\Builder
 */
class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Builder();
    }

    public function testBuild()
    {
        $raw = [
            'todoId'         => 'todoId',
            'name'           => 'name',
            'userId'         => 'userId',
            'userName'       => 'userName',
            'description'    => 'description',
            'status'         => 'status',
            'deadline'       => 'deadline',
            'createdAt'      => 'createdAt',
            'lastChange'     => 'lastChange',
            'cronExpression' => 'cronExpression',
        ];

        $actual = $this->subject->build($raw);

        $expected = new TodoItemVO();
        $expected->todoId         = 'todoId';
        $expected->name           = 'name';
        $expected->userId         = 'userId';
        $expected->userName       = 'userName';
        $expected->description    = 'description';
        $expected->status         = 'status';
        $expected->deadline       = 'deadline';
        $expected->createdAt      = 'createdAt';
        $expected->lastChange     = 'lastChange';
        $expected->cronExpression = 'cronExpression';

        $this->assertEquals($expected, $actual);
    }
}
