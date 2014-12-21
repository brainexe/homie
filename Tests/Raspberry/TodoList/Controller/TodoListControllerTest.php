<?php

namespace Tests\Raspberry\TodoList\Controller\TodoListController;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\Controller\TodoListController;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Raspberry\TodoList\ShoppingList;
use Raspberry\TodoList\VO\TodoItemVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\TodoList\Controller\TodoListController
 */
class TodoListControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TodoListController
     */
    private $subject;

    /**
     * @var TodoList|MockObject
     */
    private $mockTodoList;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $mockDatabaseUserProvider;

    /**
     * @var ShoppingList|MockObject
     */
    private $mockShoppingList;


    public function setUp()
    {
        $this->mockTodoList             = $this->getMock(TodoList::class, [], [], '', false);
        $this->mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->mockShoppingList         = $this->getMock(ShoppingList::class, [], [], '', false);

        $this->subject = new TodoListController(
            $this->mockTodoList,
            $this->mockDatabaseUserProvider,
            $this->mockShoppingList
        );
    }

    public function testIndex()
    {
        $list          = ['list'];
        $shopping_list = 'shopping_list';
        $userNames    = [$userId = 'user_id' => $userName = 'user_nam'];

        $this->mockTodoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn($list);

        $this->mockShoppingList
            ->expects($this->once())
            ->method('getShoppingListItems')
            ->willReturn($shopping_list);

        $this->mockDatabaseUserProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($userNames);

        $actualResult   = $this->subject->index();
        $expectedResult = new JsonResponse(
            [
                'list' => $list,
                'shopping_list' => $shopping_list,
                'user_names' => [$userName => $userId]
            ]
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testFetchList()
    {
        $list = [];

        $this->mockTodoList->expects($this->once())->method('getList')->will($this->returnValue($list));

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
        $request->request->set('deadline', $deadline_str = 'tomorrow');

        $itemVo              = new TodoItemVO();
        $itemVo->name        = $name;
        $itemVo->deadline    = strtotime($deadline_str); // todo TimeTrait
        $itemVo->description = $description;

        $this->mockTodoList->expects($this->once())->method('addItem')->with($user, $itemVo);

        $actualResult = $this->subject->addItem($request);

        $expectedResult = new JsonResponse($itemVo);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddShoppingListItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->mockShoppingList->expects($this->once())->method('addShoppingListItem')->with($name);

        $actualResult = $this->subject->addShoppingListItem($request);

        $expectedResult = new JsonResponse(true);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRemoveShoppingListItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->mockShoppingList->expects($this->once())->method('removeShoppingListItem')->with($name);

        $actualResult = $this->subject->removeShoppingListItem($request);

        $expectedResult = new JsonResponse(true);
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

        $this->mockTodoList
            ->expects($this->once())
            ->method('editItem')
            ->with($itemId, $changes)
            ->will($this->returnValue($itemVo));

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
        $request->request->set('user_id', $userId);

        $userVo = new UserVO();
        $userVo->username = $userName = 'name';
        $itemVo = new TodoItemVO();

        $changes = [
            'user_id' => $userId,
            'user_name' => $userName
        ];

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserById')
        ->with($userId)
        ->will($this->returnValue($userVo));

        $this->mockTodoList
        ->expects($this->once())
        ->method('editItem')
        ->with($itemId, $changes)
        ->will($this->returnValue($itemVo));

        $actualResult = $this->subject->setAssignee($request);

        $expectedResult = new JsonResponse($itemVo);
        $this->assertEquals($expectedResult, $actualResult);

    }

    public function testDeleteItem()
    {
        $itemId = 42;

        $request = new Request();
        $request->request->set('id', $itemId);

        $this->mockTodoList
        ->expects($this->once())
        ->method('deleteItem')
        ->with($itemId);

        $actualResult = $this->subject->deleteItem($request);

        $expectedResult = new JsonResponse(true);
        $this->assertEquals($expectedResult, $actualResult);
    }
}
