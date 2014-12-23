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
    private $subject;

    /**
     * @var TodoListGateway|MockObject
     */
    private $mockTodoListGateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var IdGenerator|MockObject
     */
    private $mockIdGenerator;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockTodoListGateway = $this->getMock(TodoListGateway::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new TodoList($this->mockTodoListGateway);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
        $this->subject->setIdGenerator($this->mockIdGenerator);
        $this->subject->setTime($this->mockTime);
    }

    public function testAddItem()
    {
        $todoId = 11880;
        $now = 1000;

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $this->mockIdGenerator
        ->expects($this->once())
        ->method('generateRandomNumericId')
        ->willReturn($todoId);

        $user = new UserVO();
        $user->id = $userId = 42;
        $user->username = $userName = 'username';

        $itemVo = new TodoItemVO();
        $itemVo->deadline = 900;

        $expectedItemVo = clone $itemVo;
        $expectedItemVo->todoId = $todoId;
        $expectedItemVo->userId = $userId;
        $expectedItemVo->userName = $userName;
        $expectedItemVo->createdAt = $expectedItemVo->lastChange = $now;
        $expectedItemVo->status = TodoItemVO::STATUS_PENDING;
        $expectedItemVo->deadline = 0;

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('addItem')
            ->with($itemVo);

        $event = new TodoListEvent($expectedItemVo, TodoListEvent::ADD);
        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $actualResult = $this->subject->addItem($user, $itemVo);
        $this->assertEquals($expectedItemVo, $actualResult);
    }

    public function testGetList()
    {
        $rawList = [
            [
              'todoId' => $todoId = 'id'
            ]
        ];

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('getList')
            ->willReturn($rawList);

        $actualResult = $this->subject->getList();

        $expectedVo = new TodoItemVO();
        $expectedVo->todoId = $todoId;

        $this->assertEquals([$todoId => $expectedVo], $actualResult);

    }

    public function testGetItemWithEmptyResult()
    {
        $itemId = 10;

        $raw = [];

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($raw);

        $actualResult = $this->subject->getItem($itemId);

        $this->assertNull($actualResult);
    }

    public function testGetItem()
    {
        $itemId = 10;

        $rawItem = [
            'todoId' => $todoId = 'todoId',
            'name' => $name = 'name',
            'userId' => $userId = 'user_id',
            'userName' => $userName = 'user_name',
            'description' => $description = 'description',
            'status' => $status = 'status',
            'deadline' => $deadline = 'deadline',
            'createdAt' => $createdAt = 'created_at',
            'lastChange' => $lastChange = 'last_change',
        ];

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($rawItem);

        $actualResult = $this->subject->getItem($itemId);

        $expectedItem = new TodoItemVO();
        $expectedItem->todoId      = $todoId;
        $expectedItem->name        = $name;
        $expectedItem->userId      = $userId;
        $expectedItem->userName    = $userName;
        $expectedItem->description = $description;
        $expectedItem->status      = $status;
        $expectedItem->deadline    = $deadline;
        $expectedItem->createdAt   = $createdAt;
        $expectedItem->lastChange  = $lastChange;

        $this->assertEquals($expectedItem, $actualResult);
    }

    public function testEditItem()
    {
        $changes = [];
        $itemId = 10;

        $itemRaw = [
            'todoId' => $itemId,
            'name' => $name = 'name',
            'userId' => $userId = 'user_id',
            'userName' => $userName = 'user_name',
            'description' => $description = 'description',
            'status' => $status = 'status',
            'deadline' => $deadline = 'deadline',
            'createdAt' => $createdAt = 'created_at',
            'lastChange' => $lastChange = 'last_change',
        ];

        $itemVo = new TodoItemVO();
        $itemVo->todoId = $itemId;
        $itemVo->name        = $name;
        $itemVo->userId      = $userId;
        $itemVo->userName    = $userName;
        $itemVo->description = $description;
        $itemVo->status      = $status;
        $itemVo->deadline    = $deadline;
        $itemVo->createdAt   = $createdAt;
        $itemVo->lastChange  = $lastChange;

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('editItem')
            ->with($itemId, $changes);

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($itemRaw);

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT);
        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $actualResult = $this->subject->editItem($itemId, $changes);

        $this->assertEquals($itemVo, $actualResult);
    }

    public function testDeleteItem()
    {
        $itemId = 10;

        $itemRaw = [
            'todoId' => $itemId,
            'name' => $name = 'name',
            'userId' => $userId = 'user_id',
            'userName' => $userName = 'user_name',
            'description' => $description = 'description',
            'status' => $status = 'status',
            'deadline' => $deadline = 'deadline',
            'createdAt' => $createdAt = 'created_at',
            'lastChange' => $lastChange = 'last_change',
        ];

        $itemVo = new TodoItemVO();
        $itemVo->todoId      = $itemId;
        $itemVo->name        = $name;
        $itemVo->userId      = $userId;
        $itemVo->userName    = $userName;
        $itemVo->description = $description;
        $itemVo->status      = $status;
        $itemVo->deadline    = $deadline;
        $itemVo->createdAt   = $createdAt;
        $itemVo->lastChange  = $lastChange;

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('deleteItem')
            ->with($itemId);

        $this->mockTodoListGateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($itemRaw);

        $event = new TodoListEvent($itemVo, TodoListEvent::REMOVE);
        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->deleteItem($itemId);
    }
}
