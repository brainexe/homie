<?php

namespace Tests\Homie\TodoList;

use PHPUnit\Framework\TestCase;
use Homie\TodoList\Builder;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @covers \Homie\TodoList\Builder
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
            'todoId'         => 1212,
            'name'           => 'name',
            'userId'         => 3343,
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
        $expected->todoId         = 1212;
        $expected->name           = 'name';
        $expected->userId         = 3343;
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
