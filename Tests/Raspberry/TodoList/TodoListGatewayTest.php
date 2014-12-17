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
class TodoListGatewayTest extends PHPUnit_Framework_TestCase {

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

	public function setUp() {
		$this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->mockTime = $this->getMock(Time::class, [], [], '', false);

		$this->subject = new TodoListGateway();
		$this->subject->setRedis($this->mockRedis);
		$this->subject->setTime($this->mockTime);
	}

	public function testAddItem() {
		$item_vo = new TodoItemVO();
		$item_vo->id = $id = 10;

		$this->mockRedis
			->expects($this->once())
			->method('HMSET')
			->with("todo:$id", $this->isType('array'));

		$this->mockRedis
			->expects($this->once())
			->method('sAdd')
			->with(TodoListGateway::TODO_IDS, $item_vo->id);

		$this->subject->addItem($item_vo);
	}

	public function testGetList() {

		$item_raw_1 = [
			'id' => $item_1_id = 42
		];

		$item_raw_2 = [
			'id' => $item_2_id = 43
		];

		$this->mockRedis
			->expects($this->at(0))
			->method('sMembers')
			->with("todo_ids")
			->will($this->returnValue([$item_1_id, $item_2_id]));

		$this->mockRedis
			->expects($this->at(1))
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->at(2))
			->method('HGETALL')
			->with("todo:$item_1_id")
			->will($this->returnValue($item_raw_1));

		$this->mockRedis
			->expects($this->at(3))
			->method('HGETALL')
			->with("todo:$item_2_id")
			->will($this->returnValue($item_raw_2));

		$this->mockRedis
			->expects($this->at(4))
			->method('exec')
			->will($this->returnValue([$item_raw_1, $item_raw_2]));

		$actual_result = $this->subject->getList();
		$this->assertEquals([$item_raw_1, $item_raw_2], $actual_result);
	}

	public function testGetRawItem() {
		$item_id = 10;

		$item_raw = [
			'id' => $item_id
		];

		$this->mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("todo:$item_id")
			->will($this->returnValue($item_raw));

		$actual_result = $this->subject->getRawItem($item_id);

		$this->assertEquals($item_raw, $actual_result);
	}

	public function testEditItem() {
		$item_id = 10;
		$now = 1000;

		$changes = ['name' => 'change'];

		$this->mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$changes['last_change'] = $now;
		$this->mockRedis
			->expects($this->once())
			->method('hMSet')
			->with("todo:$item_id", $changes);

		$this->subject->editItem($item_id, $changes);
	}

	public function testDeleteItem() {
		$item_id = 10;

		$this->mockRedis
			->expects($this->once())
			->method('del')
			->with("todo:$item_id");

		$this->mockRedis
			->expects($this->once())
			->method('sRem')
			->with(TodoListGateway::TODO_IDS, $item_id);

		$this->subject->deleteItem($item_id);
	}

}
