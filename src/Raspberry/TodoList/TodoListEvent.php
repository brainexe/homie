<?php

namespace Raspberry\TodoList;

use Matze\Core\EventDispatcher\AbstractEvent;
use Matze\Core\EventDispatcher\PushViaWebsocketInterface;

class TodoListEvent extends AbstractEvent implements PushViaWebsocketInterface {
	const ADD = 'todo.add';
	const REMOVE = 'todo.remove';
	const EDIT = 'todo.edit';

	/**
	 * @var TodoItemVO
	 */
	public $item_vo;

	/**
	 * @param TodoItemVO $item_vo
	 * @param string$event_name
	 */
	public function __construct(TodoItemVO $item_vo, $event_name) {
		parent::__construct($event_name);
		$this->item_vo = $item_vo;
	}
}