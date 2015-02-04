<?php

namespace Raspberry\TodoList\Controller;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Controller\ControllerInterface;
use Raspberry\TodoList\ShoppingList;
use Raspberry\TodoList\TodoList;
use Raspberry\TodoList\VO\TodoItemVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class TodoListController implements ControllerInterface
{

    /**
     * @var TodoList
     */
    private $todo;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @var ShoppingList
     */
    private $shoppingList;

    /**
     * @Inject({"@TodoList", "@DatabaseUserProvider", "@ShoppingList"})
     * @param TodoList $todo
     * @param DatabaseUserProvider $userProvider
     * @param ShoppingList $list
     */
    public function __construct(
        TodoList $todo,
        DatabaseUserProvider $userProvider,
        ShoppingList $list
    ) {
        $this->todo         = $todo;
        $this->userProvider = $userProvider;
        $this->shoppingList = $list;
    }

    /**
     * @Route("/todo/", name="todo.index")
     * @return JsonResponse
     */
    public function index()
    {
        $list         = $this->todo->getList();
        $shoppingList = $this->shoppingList->getShoppingListItems();
        $userNames    = $this->userProvider->getAllUserNames();

        return new JsonResponse([
            'list' => $list,
            'shoppingList' => $shoppingList,
            'userNames' => array_flip($userNames)
        ]);
    }

    /**
     * @Route("/todo/list/", name="todo.list")
     * @return JsonResponse
     */
    public function fetchList()
    {
        $list = $this->todo->getList();

        return new JsonResponse($list);
    }

    /**
     * @param Request $request
     * @Route("/todo/add/", name="todo.add")
     * @return JsonResponse
     */
    public function addItem(Request $request)
    {
        $itemVo = new TodoItemVO();
        $itemVo->name = $request->request->get('name');
        $itemVo->description = $request->request->get('description');
        $itemVo->deadline = strtotime($request->request->get('deadline'));

        $user = $request->attributes->get('user');

        $this->todo->addItem($user, $itemVo);

        return new JsonResponse($itemVo);
    }

    /**
     * @param Request $request
     * @Route("/todo/shopping/add/", name="todo.shopping.add")
     * @return JsonResponse
     */
    public function addShoppingListItem(Request $request)
    {
        $name = $request->request->get('name');

        $this->shoppingList->addShoppingListItem($name);

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @Route("/todo/shopping/remove/", name="todo.shopping.remove")
     * @return JsonResponse
     */
    public function removeShoppingListItem(Request $request)
    {
        $name = $request->request->get('name');

        $this->shoppingList->removeShoppingListItem($name);

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @Route("/todo/edit/", name="todo.edit")
     * @return JsonResponse
     */
    public function setItemStatus(Request $request)
    {
        $itemId = $request->request->getInt('id');
        $changes = $request->request->get('changes');

        $itemVo = $this->todo->editItem($itemId, $changes);

        return new JsonResponse($itemVo);
    }

    /**
     * @param Request $request
     * @Route("/todo/assign/", name="todo.assign")
     * @return JsonResponse
     */
    public function setAssignee(Request $request)
    {
        $itemId = $request->request->getInt('id');
        $userId = $request->request->getInt('userId');

        $user = $this->userProvider->loadUserById($userId);

        $itemVo = $this->todo->editItem($itemId, [
        'user_id' => $userId,
        'user_name' => $user->username,
        ]);
        return new JsonResponse($itemVo);
    }

    /**
     * @param Request $request
     * @Route("/todo/delete/", name="todo.delete")
     * @return JsonResponse
     */
    public function deleteItem(Request $request)
    {
        $itemId = $request->request->getInt('id');

        $this->todo->deleteItem($itemId);

        return new JsonResponse(true);
    }
}
