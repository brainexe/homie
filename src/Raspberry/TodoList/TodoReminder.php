<?php

namespace Raspberry\TodoList;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\TodoList\VO\TodoItemVO;

/**
 * @Service(public=false)
 */
class TodoReminder {

	use EventDispatcherTrait;

	/**
	 * @var TodoList
	 */
	private $todoList;

	/**
	 * @Inject({"@TodoList"})
	 * @param TodoList $todo_list
	 */
	public function __construct(TodoList $todo_list) {
		$this->todoList = $todo_list;
	}

	public function sendNotification() {
		$issues_per_state = $this->_getGroupedIssues();

		if (empty($issues_per_state)) {
			return;
		}

		$this->_sendNotification($issues_per_state);
	}

	/**
	 * @param $issues_per_state
	 */
	private function _sendNotification($issues_per_state) {
		$text = _('Erinnerung');
		$text .= ': ';

		foreach ($issues_per_state as $state => $issues_per_status) {
			$text .= $this->_getStateName(count($issues_per_status), $state);
			$text .= ': ';

			/** @var TodoItemVO $todo */
			foreach ($issues_per_status as $todo) {
				$text .= sprintf('%s: %s. ', $todo->user_name, $todo->name);
			}
		}

		$espeak_vo = new EspeakVO($text);
		$event     = new EspeakEvent($espeak_vo);

		$this->dispatchInBackground($event);
	}

	/**
	 * @return array[]
	 */
	private function _getGroupedIssues() {
		$todos = $this->todoList->getList();

		$issues_per_state = [];
		foreach ($todos as $todo) {
			if (TodoItemVO::STATUS_COMPLETED === $todo->status) {
				continue;
			}

			$issues_per_state[$todo->status][] = $todo;
		}

		return $issues_per_state;
	}

	/**
	 * @param integer $count
	 * @param string $state
	 * @return string
	 */
	private function _getStateName($count, $state) {
		switch ($state) {
			case TodoItemVO::STATUS_PROGRESS:
				return sprintf(ngettext('%d Aufgabe in Arbeit', '%d offene Aufgaben in Arbeit', $count), $count);
			case TodoItemVO::STATUS_PENDING:
			default:
				return sprintf(ngettext('%d offene Aufgabe', '%d offene Aufgaben', $count), $count);
			}
	}
}
