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
        $user_names    = [$user_id = 'user_id' => $user_name = 'user_nam'];

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
            ->willReturn($user_names);

        $actual_result   = $this->subject->index();
        $expected_result = new JsonResponse(
            [
                'list' => $list,
                'shopping_list' => $shopping_list,
                'user_names' => [$user_name => $user_id]
            ]
        );

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testFetchList()
    {
        $list = [];

        $this->mockTodoList->expects($this->once())->method('getList')->will($this->returnValue($list));

        $actual_result = $this->subject->fetchList();

        $expected_result = new JsonResponse($list);
        $this->assertEquals($expected_result, $actual_result);
    }

    public function testAddItem()
    {
        $user    = new UserVO();
        $request = new Request();
        $request->attributes->set('user', $user);
        $request->request->set('name', $name = 'name');
        $request->request->set('description', $description = 'description');
        $request->request->set('deadline', $deadline_str = 'tomorrow');

        $item_vo              = new TodoItemVO();
        $item_vo->name        = $name;
        $item_vo->deadline    = strtotime($deadline_str); // todo TimeTrait
        $item_vo->description = $description;

        $this->mockTodoList->expects($this->once())->method('addItem')->with($user, $item_vo);

        $actual_result = $this->subject->addItem($request);

        $expected_result = new JsonResponse($item_vo);
        $this->assertEquals($expected_result, $actual_result);
    }

    public function testAddShoppingListItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->mockShoppingList->expects($this->once())->method('addShoppingListItem')->with($name);

        $actual_result = $this->subject->addShoppingListItem($request);

        $expected_result = new JsonResponse(true);
        $this->assertEquals($expected_result, $actual_result);
    }

    public function testRemoveShoppingListItem()
    {
        $name = 'name';

        $request = new Request();
        $request->request->set('name', $name);

        $this->mockShoppingList->expects($this->once())->method('removeShoppingListItem')->with($name);

        $actual_result = $this->subject->removeShoppingListItem($request);

        $expected_result = new JsonResponse(true);
        $this->assertEquals($expected_result, $actual_result);
    }

    public function testSetItemStatus()
    {
        $item_id = 10;
        $changes = ['changes'];

        $request = new Request();
        $request->request->set('id', $item_id);
        $request->request->set('changes', $changes);

        $item_vo = new TodoItemVO();

        $this->mockTodoList
        ->expects($this->once())
        ->method('editItem')
        ->with($item_id, $changes)
        ->will($this->returnValue($item_vo));

        $actual_result = $this->subject->setItemStatus($request);

        $expected_result = new JsonResponse($item_vo);
        $this->assertEquals($expected_result, $actual_result);
    }

    public function testSetAssignee()
    {
        $item_id = 10;
        $user_id = 42;

        $request = new Request();
        $request->request->set('id', $item_id);
        $request->request->set('user_id', $user_id);

        $user_vo = new UserVO();
        $user_vo->username = $user_name = 'name';
        $item_vo = new TodoItemVO();

        $changes = [
        'user_id' => $user_id,
        'user_name' => $user_name
        ];

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('loadUserById')
        ->with($user_id)
        ->will($this->returnValue($user_vo));

        $this->mockTodoList
        ->expects($this->once())
        ->method('editItem')
        ->with($item_id, $changes)
        ->will($this->returnValue($item_vo));

        $actual_result = $this->subject->setAssignee($request);

        $expected_result = new JsonResponse($item_vo);
        $this->assertEquals($expected_result, $actual_result);

    }

    public function testDeleteItem()
    {
        $item_id = 42;

        $request = new Request();
        $request->request->set('id', $item_id);

        $this->mockTodoList
        ->expects($this->once())
        ->method('deleteItem')
        ->with($item_id);

        $actual_result = $this->subject->deleteItem($request);

        $expected_result = new JsonResponse(true);
        $this->assertEquals($expected_result, $actual_result);
    }
}
