<?php

namespace Raspberry\Dashboard;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class Dashboard {

	use RedisTrait;

	/**
	 * @var WidgetFactory
	 */
	private $_widget_factory;

	/**
	 * @Inject("@WidgetFactory")
	 */
	public function __construct(WidgetFactory $widget_factory) {
		$this->_widget_factory = $widget_factory;
	}

	/**
	 * @param integer $user_id
	 * @return WidgetInterface[]
	 */
	public function getWidgets($user_id) {
		$widgets = [];

		return $widgets;
	}

} 
