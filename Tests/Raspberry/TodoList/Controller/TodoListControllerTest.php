<?php

namespace Tests\Raspberry\TodoList\Controller;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\Controller\TodoListController;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Raspberry\TodoList\VO\TodoItemVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\TodoList\Controller\TodoListController
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
     * @var DatabaseUserProvider|MockObject
     */
    private $userProvider;

    public function setUp()
    {
        $this->todoList     = $this->getMock(TodoList::class, [], [], '', false);
        $this->userProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

        $this->subject = new TodoListController(
            $this->todoList,
            $this->userProvider
        );
    }

    public function testIndex()
    {
        $list         = ['list'];
        $userNames    = [$userId = 'user_id' => $userName = 'user_nam'];

        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn($list);

        $this->userProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($userNames);

        $actualResult   = $this->subject->index();
        $expectedResult = new JsonResponse(
            [
                'list' => $list,
                'userNames' => [$userName => $userId]
            ]
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testFetchList()
    {
        $list = [];

        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn($list);

        $actualResult = $this->subject->fetchList();

        $expectedResult = new JsonResponse($list);
        $this->assertEquals($expectedResult, $actualResult);
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
        $itemVo->deadline    = strtotime($deadlineStr); // todo add to TimeTrait
        $itemVo->description = $description;

        $this->todoList
            ->expects($this->once())
            ->method('addItem')
            ->with($user, $itemVo);

        $actualResult = $this->subject->addItem($request);
        $expectedResult = new JsonResponse($itemVo);

        $this->assertEquals($expectedResult, $actualResult);
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

        $actualResult = $this->subject->setItemStatus($request);

        $expectedResult = new JsonResponse($itemVo);
        $this->assertEquals($expectedResult, $actualResult);
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

        $this->userProvider
            ->expects($this->once())
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);

        $this->todoList
            ->expects($this->once())
            ->method('editItem')
            ->with($itemId, $changes)
            ->willReturn($itemVo);

        $actualResult = $this->subject->setAssignee($request);

        $expectedResult = new JsonResponse($itemVo);
        $this->assertEquals($expectedResult, $actualResult);

    }

    public function testDeleteItem()
    {
        $itemId = 42;

        $request = new Request();
        $request->request->set('id', $itemId);

        $this->todoList
            ->expects($this->once())
            ->method('deleteItem')
            ->with($itemId);

        $actualResult = $this->subject->deleteItem($request);

        $expectedResult = new JsonResponse(true);
        $this->assertEquals($expectedResult, $actualResult);
    }
}
