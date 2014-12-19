<?php

namespace Raspberry\Dashboard;

use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @service(public=false)
 */
class DashboardGateway {

	use RedisTrait;
	use IdGeneratorTrait;

	const REDIS_DASHBOARD = 'dashboard:%s';

	/**
	 * @param integer $user_id
	 * @return array[]
	 */
	public function getDashboard($user_id) {
		$dashboard = [];

		$widgets_raw = $this->getRedis()->hGetAll($this->_getKey($user_id));

		foreach ($widgets_raw as $id => $widget_raw) {
			$widget = json_decode($widget_raw, true);
			$widget['id']   = $id;
			$widget['open'] = true;
			$dashboard[] = $widget;
		}

		return $dashboard;
	}

	/**
	 * @param integer $user_id
	 * @param array $payload
	 */
	public function addWidget($user_id, array $payload) {
		$new_id = $this->generateRandomNumericId();
		$this->getRedis()->HSET($this->_getKey($user_id), $new_id, json_encode($payload));
	}

	/**
	 * @param integer $user_id
	 * @param integer $widget_id
	 */
	public function deleteWidget($user_id, $widget_id) {
		$this->getRedis()->HDEL($this->_getKey($user_id), $widget_id);

	}

	/**
	 * @param integer $user_id
	 * @return string
	 */
	private function _getKey($user_id) {
		return sprintf(self::REDIS_DASHBOARD, $user_id);
	}
}
