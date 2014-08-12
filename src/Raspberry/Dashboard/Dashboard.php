<?php

namespace Raspberry\Dashboard;

use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class Dashboard {

	use RedisTrait;

	const REDIS_DASHBOARD = 'dashboard:%s';

	/**
	 * @var WidgetFactory
	 */
	private $_widget_factory;

	/**
	 * @Inject("@WidgetFactory")
	 * @param WidgetFactory $widget_factory
	 */
	public function __construct(WidgetFactory $widget_factory) {
		$this->_widget_factory = $widget_factory;
	}

	/**
	 * @param integer $user_id
	 * @return array[][]
	 */
	public function getDashboard($user_id) {
		$dashboard = [];

		$widgets_raw = $this->getRedis()->hGetAll($this->_getKey($user_id));

		foreach ($widgets_raw as $i => $widget_raw) {
			$dashboard[$i % 2][] = json_decode($widget_raw, true);
		}

		return $dashboard;
	}

	/**
	 * @param integer $user_id
	 * @return string
	 */
	private function _getKey($user_id) {
		return sprintf(self::REDIS_DASHBOARD, $user_id);
	}

	/**
	 * @return string[]
	 */
	public function getAvailableWidgets() {
		return $this->_widget_factory->getAvailableWidgets();
	}

	/**
	 * @param integer $user_id
	 * @param string $type
	 * @param array $payload
	 */
	public function addWidget($user_id, $type, $payload) {
		$widget = $this->_widget_factory->getWidget($type);
		$widget->validate($payload);

		$payload['type'] = $type;

		$new_id = mt_rand(1000, 1000000);
		$this->getRedis()->HSET($this->_getKey($user_id), $new_id, json_encode($payload));
	}

} 
