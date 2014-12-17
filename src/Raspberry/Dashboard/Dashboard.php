<?php

namespace Raspberry\Dashboard;

use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class Dashboard {

	use RedisTrait;
	use IdGeneratorTrait;

	const REDIS_DASHBOARD = 'dashboard:%s';

	/**
	 * @var WidgetFactory
	 */
	private $widgetFactory;

	/**
	 * @Inject("@WidgetFactory")
	 * @param WidgetFactory $widget_factory
	 */
	public function __construct(WidgetFactory $widget_factory) {
		$this->widgetFactory = $widget_factory;
	}

	/**
	 * @param integer $user_id
	 * @return array[]
	 */
	public function getDashboard($user_id) {
		$dashboard = [];

		$widgets_raw = $this->getRedis()->hGetAll($this->_getKey($user_id));

		foreach ($widgets_raw as $id => $widget_raw) {
			$widget = json_decode($widget_raw, true);
			$widget['id'] = $id;
			$widget['open'] = true;
			$dashboard[] = $widget;
		}

		return $dashboard;
	}

	/**
	 * @return string[]
	 */
	public function getAvailableWidgets() {
		return $this->widgetFactory->getAvailableWidgets();
	}

	/**
	 * @param integer $user_id
	 * @param string $type
	 * @param array $payload
	 */
	public function addWidget($user_id, $type, $payload) {
		$widget = $this->widgetFactory->getWidget($type);
		$widget->validate($payload);

		$payload['type'] = $type;

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
