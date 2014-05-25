<?php

namespace Raspberry\TodoList;
use Matze\Core\Authentication\UserVO;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\IdGeneratorTrait;
use Matze\Core\Util\IdGenerator;

/**
 * @Service(public=false)
 */
class TodoList {

	use EventDispatcherTrait;
	use IdGeneratorTrait;

	/**
	 * @var TodoListGateway
	 */
	private $_todo_list_gateway;


	/**
	 * @Inject({"@TodoListGateway"})
	 */
	public function __construct(TodoListGateway $todo_list_gateway) {
		$this->_todo_list_gateway = $todo_list_gateway;
	}

	/**
	 * @param UserVO $user
	 * @param TodoItemVO $item_vo
	 * @return TodoItemVO
	 */
	public function addItem(UserVO $user, TodoItemVO $item_vo) {
		$item_vo->id = $this->generateRandomNumericId();
		$item_vo->user_id = $user->id;
		$item_vo->user_name = $user->username;
		$item_vo->created_at = $item_vo->last_change = time();
		$item_vo->status = TodoItemVO::STATUS_PENDING;

		$this->_todo_list_gateway->addItem($item_vo);

		$event = new TodoListEvent($item_vo, TodoListEvent::ADD);
		$this->dispatchEvent($event);

		return $item_vo;
	}

	/**
	 * @return TodoItemVO[]
	 */
	public function getList() {
		$list = [];
		$raw_list = $this->_todo_list_gateway->getList();

		foreach ($raw_list as $item) {
			$item_vo = new TodoItemVO();
			$item_vo->fillValues($item);
			$list[$item['id']] = $item_vo;
		}

		return $list;
	}

	/**
	 * @param integer $item_id
	 * @return null|TodoItemVO
	 */
	public function getItem($item_id) {
		$raw_item = $this->_todo_list_gateway->getRawItem($item_id);

		if (empty($raw_item)) {
			return null;
		}

		$item_vo = new TodoItemVO();
		$item_vo->fillValues($raw_item);

		return $item_vo;
	}

	/**
	 * @param int $item_id
	 * @param array $changes
	 * @return TodoItemVO
	 */
	public function editItem($item_id, array $changes) {
		$this->_todo_list_gateway->editItem($item_id, $changes);

		$item_vo = $this->getItem($item_id);

		$event = new TodoListEvent($item_vo, TodoListEvent::EDIT);
		$this->dispatchEvent($event);

		return $item_vo;
	}

	/**
	 * @param int $item_id
	 */
	public function deleteItem($item_id) {
		$item_vo = $this->getItem($item_id);

		$this->_todo_list_gateway->deleteItem($item_id);

		$event = new TodoListEvent($item_vo, TodoListEvent::REMOVE);
		$this->dispatchEvent($event);
	}
} 