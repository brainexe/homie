<?php

namespace Raspberry\TodoList\Controller;

use Matze\Core\Authentication\DatabaseUserProvider;
use Matze\Core\Controller\AbstractController;
use Raspberry\TodoList\ShoppingList;
use Raspberry\TodoList\TodoList;
use Raspberry\TodoList\VO\TodoItemVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class TodoListController extends AbstractController {

	/**
	 * @var TodoList
	 */
	private $_todo_list;

	/**
	 * @var DatabaseUserProvider
	 */
	private $_database_user_provider;
	/**
	 * @var ShoppingList
	 */
	private $_shopping_list;

	/**
	 * @Inject({"@TodoList", "@DatabaseUserProvider", "@ShoppingList"})
	 */
	public function __construct(TodoList $todo_list, DatabaseUserProvider $database_user_provider, ShoppingList $shopping_list) {
		$this->_todo_list = $todo_list;
		$this->_database_user_provider = $database_user_provider;
		$this->_shopping_list = $shopping_list;
	}

	/**
	 * @param Request $request
	 * @Route("/todo/", name="todo.index")
	 * @return JsonResponse
	 */
	public function index(Request $request) {
		return new JsonResponse([
			'list' => $this->_todo_list->getList(),
			'shopping_list' => $this->_shopping_list->getShoppingListItems(),
			'user_names' => array_flip($this->_database_user_provider->getAllUserNames())
		]);
	}

	/**
	 * @param Request $request
	 * @Route("/todo/list/", name="todo.list")
	 * @return JsonResponse
	 */
	public function fetchList(Request $request) {
		$list = $this->_todo_list->getList();

		return new JsonResponse($list);
	}

	/**
	 * @param Request $request
	 * @Route("/todo/add/", name="todo.add")
	 * @return JsonResponse
	 */
	public function addItem(Request $request) {
		$item_vo = new TodoItemVO();
		$item_vo->name = $request->request->get('name');
		$item_vo->description = $request->request->get('description');
		$item_vo->deadline = strtotime($request->request->get('deadline'));

		$user = $this->_getCurrentUser($request);

		$this->_todo_list->addItem($user, $item_vo);

		return new JsonResponse($item_vo);
	}

	/**
	 * @param Request $request
	 * @Route("/todo/shopping/add/", name="todo.shopping.add")
	 * @return JsonResponse
	 */
	public function addShoppingListItem(Request $request) {
		$name = $request->request->get('name');

		$this->_shopping_list->addShoppingListItem($name);

		return new JsonResponse(true);
	}

	/**
	 * @param Request $request
	 * @Route("/todo/shopping/remove/", name="todo.shopping.remove")
	 * @return JsonResponse
	 */
	public function removeShoppingListItem(Request $request) {
		$name = $request->request->get('name');

		$this->_shopping_list->removeShoppingListItem($name);

		return new JsonResponse(true);
	}

	/**
	 * @param Request $request
	 * @Route("/todo/edit/", name="todo.edit")
	 * @return JsonResponse
	 */
	public function setItemStatus(Request $request) {
		$item_id = $request->request->getInt('id');

		$changes = $request->request->get('changes');
		$item_vo = $this->_todo_list->editItem($item_id, $changes);

		return new JsonResponse($item_vo);
	}

	/**
	 * @param Request $request
	 * @Route("/todo/assign/", name="todo.assign")
	 * @return JsonResponse
	 */
	public function setAssignee(Request $request) {
		$item_id = $request->request->getInt('id');
		$user_id = $request->request->getInt('user_id');

		$user = $this->_database_user_provider->loadUserById($user_id);

		$item_vo = $this->_todo_list->editItem($item_id, [
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
	public function deleteItem(Request $request) {
		$item_id = $request->request->getInt('id');

		$this->_todo_list->deleteItem($item_id);

		return new JsonResponse(true);
	}
} 