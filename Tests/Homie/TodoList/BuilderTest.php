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
            'todoId'      => 'todoId',
            'name'        => 'name',
            'userId'      => 'userId',
            'userName'    => 'userName',
            'description' => 'description',
            'status'      => 'status',
            'deadline'    => 'deadline',
            'createdAt'   => 'createdAt',
            'lastChange'  => 'lastChange',
        ];

        $actualResult = $this->subject->build($raw);

        $expectedResult = new TodoItemVO();
        $expectedResult->todoId      = 'todoId';
        $expectedResult->name        = 'name';
        $expectedResult->userId      = 'userId';
        $expectedResult->userName    = 'userName';
        $expectedResult->description = 'description';
        $expectedResult->status      = 'status';
        $expectedResult->deadline    = 'deadline';
        $expectedResult->createdAt   = 'createdAt';
        $expectedResult->lastChange  = 'lastChange';

        $this->assertEquals($expectedResult, $actualResult);
    }
}
