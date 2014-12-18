<?php

namespace Raspberry\Dashboard;
use InvalidArgumentException;

/**
 * @Service(public=false)
 */
class WidgetFactory {

	/**
	 * @var WidgetInterface[]
	 */
	private $widgets;

	/**
	 * @param string $widget_id
	 * @param WidgetInterface $widget
	 */
	public function addWidget($widget_id, WidgetInterface $widget) {
		$this->widgets[$widget_id] = $widget;
	}

	/**
	 * @param string $id
	 * @return WidgetInterface
	 * @throws InvalidArgumentException
	 */
	public function getWidget($id) {
		if (empty($this->widgets[$id])) {
			throw new InvalidArgumentException(sprintf('Invalid widget: %s', $id));
		}

		return $this->widgets[$id];
	}

	/**
	 * @return string[]
	 */
	public function getAvailableWidgets() {
		return $this->widgets;
	}

}
