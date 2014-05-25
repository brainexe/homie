<?php

namespace Raspberry\TodoList;

use Matze\Core\Authentication\UserVO;
use Matze\Core\Controller\AbstractController;
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
	 * @Inject({"@TodoList"})
	 */
	public function __construct(TodoList $todo_list) {
		$this->_todo_list = $todo_list;
	}

	/**
	 * @param Request $request
	 * @Route("/todo/", name="todo.index")
	 * @return string
	 */
	public function index(Request $request) {
		return $this->render('todo/index.html.twig', [
			'list' => $this->_todo_list->getList()
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
	 * @Route("/todo/delete/", name="todo.delete")
	 * @return JsonResponse
	 */
	public function deleteItem(Request $request) {
		$item_id = $request->request->getInt('id');

		$this->_todo_list->deleteItem($item_id);

		return new JsonResponse(true);
	}
} 