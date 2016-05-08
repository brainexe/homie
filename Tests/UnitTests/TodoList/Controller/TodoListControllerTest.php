<?php

namespace Tests\Homie\TodoList\Controller;

use ArrayIterator;
use BrainExe\Core\Authentication\LoadUser;
use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\Controller\TodoListController;
use Homie\TodoList\TodoList;
use Homie\TodoList\VO\TodoItemVO;

use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\TodoList\Controller\TodoListController
 */
class TodoListControllerTest extends TestCase
{

    /**
     * @var TodoListController
     */
    private $subject;

    /**
     * @var TodoList|MockObject
     */
    private $todoList;

    /**
     * @var LoadUser|MockObject
     */
    private $loadUser;

    public function setUp()
    {
        $this->todoList = $this->getMockWithoutInvokingTheOriginalConstructor(TodoList::class);
        $this->loadUser = $this->getMockWithoutInvokingTheOriginalConstructor(LoadUser::class);

        $this->subject = new TodoListController(
            $this->todoList,
            $this->loadUser
        );
    }

    public function testIndex()
    {
        $list = ['list'];

        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn(new ArrayIterator($list));

        $actual = $this->subject->index();

        $this->assertEquals($list, $actual['list']);
    }

    public function testAddItem()
    {
        $user    = new UserVO();
        $request = new Request();
        $request->attributes->set('user', $user);
        $request->request->set('name', $name = 'name');
        $request->request->set('description', $description = 'description');
        $request->request->set('deadline', $deadlineStr = 'tomorrow');

        $itemVo              = new TodoItemVO();
        $itemVo->name        = $name;
        $itemVo->deadline    = strtotime($deadlineStr);
        $itemVo->description = $description;

        $this->todoList
            ->expects($this->once())
            ->method('addItem')
            ->with($user, $itemVo);

        $actual = $this->subject->addItem($request);

        $this->assertEquals($itemVo, $actual);
    }

    public function testSetItemStatus()
    {
        $itemId = 10;
        $changes = ['changes'];

        $request = new Request();
        $request->request->set('id', $itemId);
        $request->request->set('changes', $changes);

        $itemVo = new TodoItemVO();

        $this->todoList
            ->expects($this->once())
            ->method('editItem')
            ->with($itemId, $changes)
            ->willReturn($itemVo);

        $actual = $this->subject->editItem($request);

        $this->assertEquals($itemVo, $actual);
    }

    public function testSetAssignee()
    {
        $itemId = 10;
        $userId = 42;

        $request = new Request();
        $request->request->set('id', $itemId);
        $request->request->set('userId', $userId);

        $userVo = new UserVO();
        $userVo->username = $userName = 'name';
        $itemVo = new TodoItemVO();

        $changes = [
            'userId' => $userId,
            'userName' => $userName
        ];

        $this->loadUser
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);

        $this->todoList
            ->expects($this->once())
            ->method('editItem')
            ->with($itemId, $changes)
            ->willReturn($itemVo);

        $actual = $this->subject->setAssignee($request);

        $this->assertEquals($itemVo, $actual);
    }

    public function testDeleteItem()
    {
        $itemId = 42;

        $request = new Request();

        $this->todoList
            ->expects($this->once())
            ->method('deleteItem')
            ->with($itemId);

        $actual = $this->subject->deleteItem($request, $itemId);

        $this->assertTrue($actual);
    }
}
