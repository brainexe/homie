<?php

namespace Raspberry\TodoList\Controller;

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
    public function __construct(TodoList $todo, DatabaseUserProvider $userProvider, ShoppingList $list)
    {
        $this->todo = $todo;
        $this->userProvider = $userProvider;
        $this->shoppingList = $list;
    }

    /**
     * @Route("/todo/", name="todo.index")
     * @return JsonResponse
     */
    public function index()
    {
        $list = $this->todo->getList();
        $shopping_list = $this->shoppingList->getShoppingListItems();
        $user_names = $this->userProvider->getAllUserNames();

        return new JsonResponse([
        'list' => $list,
        'shopping_list' => $shopping_list,
        'user_names' => array_flip($user_names)
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
        $item_vo = new TodoItemVO();
        $item_vo->name = $request->request->get('name');
        $item_vo->description = $request->request->get('description');
        $item_vo->deadline = strtotime($request->request->get('deadline'));

        $user = $request->attributes->get('user');

        $this->todo->addItem($user, $item_vo);

        return new JsonResponse($item_vo);
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
        $item_id = $request->request->getInt('id');
        $changes = $request->request->get('changes');

        $item_vo = $this->todo->editItem($item_id, $changes);

        return new JsonResponse($item_vo);
    }

    /**
     * @param Request $request
     * @Route("/todo/assign/", name="todo.assign")
     * @return JsonResponse
     */
    public function setAssignee(Request $request)
    {
        $item_id = $request->request->getInt('id');
        $user_id = $request->request->getInt('user_id');

        $user = $this->userProvider->loadUserById($user_id);

        $item_vo = $this->todo->editItem($item_id, [
        'user_id' => $user_id,
        'user_name' => $user->username,
        ]);
        return new JsonResponse($item_vo);
    }

    /**
     * @param Request $request
     * @Route("/todo/delete/", name="todo.delete")
     * @return JsonResponse
     */
    public function deleteItem(Request $request)
    {
        $item_id = $request->request->getInt('id');

        $this->todo->deleteItem($item_id);

        return new JsonResponse(true);
    }
}
