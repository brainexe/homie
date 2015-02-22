<?php

namespace Tests\Raspberry\TodoList\TodoList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\Builder;
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
    private $gateway;

    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    /**
     * @var Time|MockObject
     */
    private $time;

    /**
     * @var Builder|MockObject
     */
    private $builder;

    public function setUp()
    {
        $this->gateway = $this->getMock(TodoListGateway::class, [], [], '', false);
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->idGenerator     = $this->getMock(IdGenerator::class, [], [], '', false);
        $this->time            = $this->getMock(Time::class, [], [], '', false);
        $this->builder         = $this->getMock(Builder::class, [], [], '', false);

        $this->subject = new TodoList($this->gateway, $this->builder);
        $this->subject->setEventDispatcher($this->eventDispatcher);
        $this->subject->setIdGenerator($this->idGenerator);
        $this->subject->setTime($this->time);
    }

    public function testAddItem()
    {
        $todoId = 11880;
        $now    = 1000;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($todoId);

        $user           = new UserVO();
        $user->id       = $userId = 42;
        $user->username = $userName = 'username';

        $itemVo           = new TodoItemVO();
        $itemVo->deadline = 900;

        $expectedItemVo            = clone $itemVo;
        $expectedItemVo->todoId    = $todoId;
        $expectedItemVo->userId    = $userId;
        $expectedItemVo->userName  = $userName;
        $expectedItemVo->createdAt = $expectedItemVo->lastChange = $now;
        $expectedItemVo->status    = TodoItemVO::STATUS_PENDING;
        $expectedItemVo->deadline  = 0;

        $this->gateway
            ->expects($this->once())
            ->method('addItem')
            ->with($itemVo);

        $event = new TodoListEvent($expectedItemVo, TodoListEvent::ADD);
        $this->eventDispatcher
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

        $this->gateway
            ->expects($this->once())
            ->method('getList')
            ->willReturn($rawList);

        $expectedVo = new TodoItemVO();

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($rawList[0])
            ->willReturn($expectedVo);

        $actualResult = $this->subject->getList();

        $this->assertEquals([$todoId => $expectedVo], $actualResult);

    }

    public function testGetItemWithEmptyResult()
    {
        $itemId = 10;

        $raw = [];

        $this->gateway
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

        $this->gateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($rawItem);

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

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($rawItem)
            ->willReturn($expectedItem);

        $actualResult = $this->subject->getItem($itemId);

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


        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($itemRaw)
            ->willReturn($itemVo);

        $this->gateway
            ->expects($this->once())
            ->method('editItem')
            ->with($itemId, $changes);

        $this->gateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($itemRaw);

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT);
        $this->eventDispatcher
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
                'userName' => 'user_name',
                'description' => $description = 'description',
                'status' => $status = 'status',
                'deadline' => $deadline = 'deadline',
                'createdAt' => $createdAt = 'created_at',
                'lastChange' => $lastChange = 'last_change',
        ];

        $itemVo = new TodoItemVO();
        $itemVo->todoId      = $itemId;

        $this->gateway
            ->expects($this->once())
            ->method('deleteItem')
            ->with($itemId);

        $this->gateway
            ->expects($this->once())
            ->method('getRawItem')
            ->with($itemId)
            ->willReturn($itemRaw);

        $this->builder
            ->expects($this->once())
            ->method('build')
            ->with($itemRaw)
            ->willReturn($itemVo);

        $event = new TodoListEvent($itemVo, TodoListEvent::REMOVE);
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->deleteItem($itemId);
    }
}
