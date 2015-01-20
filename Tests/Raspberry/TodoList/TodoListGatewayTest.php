<?php

namespace Tests\Raspberry\TodoList\TodoListGateway;

use BrainExe\Core\Redis\Redis;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\TodoListGateway;
use Raspberry\TodoList\VO\TodoItemVO;

/**
 * @Covers Raspberry\TodoList\TodoListGateway
 */
class TodoListGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TodoListGateway
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new TodoListGateway();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setTime($this->mockTime);
    }

    public function testAddItem()
    {
        $itemVo = new TodoItemVO();
        $itemVo->todoId = $id = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('HMSET')
            ->with("todo:$id", $this->isType('array'));

        $this->mockRedis
            ->expects($this->once())
            ->method('sAdd')
            ->with(TodoListGateway::TODO_IDS, $itemVo->todoId);

        $this->subject->addItem($itemVo);
    }

    public function testGetList()
    {

        $itemRaw1 = [
        'id' => $item1Id = 42
        ];

        $itemRaw2 = [
          'id' => $item2Id = 43
        ];

        $this->mockRedis
            ->expects($this->at(0))
            ->method('sMembers')
            ->with("todo_ids")
            ->willReturn([$item1Id, $item2Id]);

        $this->mockRedis
            ->expects($this->at(1))
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->at(2))
            ->method('HGETALL')
            ->with("todo:$item1Id")
            ->willReturn($itemRaw1);

        $this->mockRedis
            ->expects($this->at(3))
            ->method('HGETALL')
            ->with("todo:$item2Id")
            ->willReturn($itemRaw2);

        $this->mockRedis
            ->expects($this->at(4))
            ->method('exec')
            ->willReturn([$itemRaw1, $itemRaw2]);

        $actualResult = $this->subject->getList();
        $this->assertEquals([$itemRaw1, $itemRaw2], $actualResult);
    }

    public function testGetRawItem()
    {
        $itemId = 10;

        $itemRaw = [
            'id' => $itemId
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("todo:$itemId")
            ->willReturn($itemRaw);

        $actualResult = $this->subject->getRawItem($itemId);

        $this->assertEquals($itemRaw, $actualResult);
    }

    public function testEditItem()
    {
        $itemId = 10;
        $now = 1000;

        $changes = [
            'name' => 'change'
        ];

        $this->mockTime
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $changes['lastChange'] = $now;
        $this->mockRedis
            ->expects($this->once())
            ->method('hMSet')
            ->with("todo:$itemId", $changes);

        $this->subject->editItem($itemId, $changes);
    }

    public function testDeleteItem()
    {
        $itemId = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('del')
            ->with("todo:$itemId");

        $this->mockRedis
            ->expects($this->once())
            ->method('sRem')
            ->with(TodoListGateway::TODO_IDS, $itemId);

        $this->subject->deleteItem($itemId);
    }
}
