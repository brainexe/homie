<?php

namespace Tests\Raspberry\TodoList\TodoList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\Authentication\UserVO;
use Raspberry\TodoList\TodoListEvent;
use Raspberry\TodoList\VO\TodoItemVO;
use Raspberry\TodoList\TodoListGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Core\Util\Time;

class TodoListTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TodoList
     */
    private $_subject;

    /**
     * @var TodoListGateway|MockObject
     */
    private $_mockTodoListGateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $_mockEventDispatcher;

    /**
     * @var IdGenerator|MockObject
     */
    private $_mockIdGenerator;

    /**
     * @var Time|MockObject
     */
    private $_mockTime;

    public function setUp()
    {
        $this->_mockTodoListGateway = $this->getMock(TodoListGateway::class, [], [], '', false);
        $this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
        $this->_mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->_subject = new TodoList($this->_mockTodoListGateway);
        $this->_subject->setEventDispatcher($this->_mockEventDispatcher);
        $this->_subject->setIdGenerator($this->_mockIdGenerator);
        $this->_subject->setTime($this->_mockTime);
    }

    public function testAddItem()
    {
        $id = 11880;
        $now = 1000;

        $this->_mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $this->_mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomNumericId')
        ->will($this->returnValue($id));

        $user = new UserVO();
        $user->id = $user_id = 42;
        $user->username = $user_name = 'username';

        $item_vo = new TodoItemVO();
        $item_vo->deadline = 900;

        $expected_item_vo = clone $item_vo;
        $expected_item_vo->id = $id;
        $expected_item_vo->user_id = $user_id;
        $expected_item_vo->user_name = $user_name;
        $expected_item_vo->created_at = $expected_item_vo->last_change = $now;
        $expected_item_vo->status = TodoItemVO::STATUS_PENDING;
        $expected_item_vo->deadline = 0;

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('addItem')
        ->with($item_vo);

        $event = new TodoListEvent($expected_item_vo, TodoListEvent::ADD);
        $this->_mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchEvent')
        ->with($event);

        $actual_result = $this->_subject->addItem($user, $item_vo);
        $this->assertEquals($expected_item_vo, $actual_result);
    }

    public function testGetList()
    {
        $raw_list = [
        [
        'id' => $id = 'id'
        ]
        ];

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('getList')
        ->will($this->returnValue($raw_list));

        $actual_result = $this->_subject->getList();

        $expected_vo = new TodoItemVO();
        $expected_vo->id = $id;

        $this->assertEquals([$id => $expected_vo], $actual_result);

    }

    public function testGetItemWithEmptyResult()
    {
        $item_id = 10;

        $raw_item = [];

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('getRawItem')
        ->with($item_id)
        ->will($this->returnValue($raw_item));

        $actual_result = $this->_subject->getItem($item_id);

        $this->assertNull($actual_result);
    }

    public function testGetItem()
    {
        $item_id = 10;

        $raw_item = [
        'id' => $id = 'id',
        'name' => $name = 'name',
        'user_id' => $user_id = 'user_id',
        'user_name' => $user_name = 'user_name',
        'description' => $description = 'description',
        'status' => $status = 'status',
        'deadline' => $deadline = 'deadline',
        'created_at' => $created_at = 'created_at',
        'last_change' => $last_change = 'last_change',
        ];

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('getRawItem')
        ->with($item_id)
        ->will($this->returnValue($raw_item));

        $actual_result = $this->_subject->getItem($item_id);

        $expected_item = new TodoItemVO();
        $expected_item->id = $id;
        $expected_item->name = $name;
        $expected_item->user_id = $user_id;
        $expected_item->user_name = $user_name;
        $expected_item->description = $description;
        $expected_item->status = $status;
        $expected_item->deadline = $deadline;
        $expected_item->created_at = $created_at;
        $expected_item->last_change = $last_change;

        $this->assertEquals($expected_item, $actual_result);
    }

    public function testEditItem()
    {
        $changes = [];
        $item_id = 10;

        $item_raw = [
        'id' => $item_id
        ];

        $item_vo = new TodoItemVO();
        $item_vo->id = $item_id;

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('editItem')
        ->with($item_id, $changes);

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('getRawItem')
        ->with($item_id)
        ->will($this->returnValue($item_raw));

        $event = new TodoListEvent($item_vo, TodoListEvent::EDIT);
        $this->_mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchEvent')
        ->with($event);

        $actual_result = $this->_subject->editItem($item_id, $changes);

        $this->assertEquals($item_vo, $actual_result);
    }

    public function testDeleteItem()
    {
        $item_id = 10;

        $item_raw = [
        'id' => $item_id
        ];

        $item_vo = new TodoItemVO();
        $item_vo->id = $item_id;

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('deleteItem')
        ->with($item_id);

        $this->_mockTodoListGateway
        ->expects($this->once())
        ->method('getRawItem')
        ->with($item_id)
        ->will($this->returnValue($item_raw));

        $event = new TodoListEvent($item_vo, TodoListEvent::REMOVE);
        $this->_mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchEvent')
        ->with($event);

        $this->_subject->deleteItem($item_id);
    }
}
