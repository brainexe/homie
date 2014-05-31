<?php

namespace Raspberry\TodoList;

use Matze\Core\Traits\RedisTrait;
use Raspberry\TodoList\VO\TodoItemVO;
use Redis;

/**
 * @Service(public=false)
 */
class TodoListGateway {

	use RedisTrait;

	const TODO_KEY = 'todo:%d';
	const TODO_IDS = 'todo_ids';

	/**
	 * @param TodoItemVO $item_vo
	 */
	public function addItem(TodoItemVO $item_vo) {
		$redis = $this->getRedis();

		$todo_raw = (array)$item_vo;

		$redis->HMSET($this->_getRedisKey($item_vo->id), $todo_raw);

		$redis->sAdd(self::TODO_IDS, $item_vo->id);
	}

	/**
	 * @return array[]
	 */
	public function getList() {
		$item_ids = $this->getRedis()->sMembers(self::TODO_IDS);

		$redis = $this->getRedis()->multi(Redis::PIPELINE);
		foreach ($item_ids as $item_id) {
			$redis->HGETALL($this->_getRedisKey($item_id));
		}

		return $redis->exec();
	}

	/**
	 * @param integer $item_id
	 * @return array
	 */
	public function getRawItem($item_id) {
		return 	$this->getRedis()->HGETALL($this->_getRedisKey($item_id));
	}

	/**
	 * @param int $item_id
	 * @param array $changes
	 */
	public function editItem($item_id, array $changes) {
		$key = $this->_getRedisKey($item_id);

		$changes['last_change'] = time();

		$this->getRedis()->hMSet($key, $changes);
	}

	/**
	 * @param integer $item_id
	 */
	public function deleteItem($item_id) {
		$key = $this->_getRedisKey($item_id);

		$this->getRedis()->del($key);
		$this->getRedis()->sRem(self::TODO_IDS, $item_id);
	}

	/**
	 * @param integer $item_id
	 * @return string
	 */
	private function _getRedisKey($item_id) {
		return sprintf(self::TODO_KEY, $item_id);
	}

}