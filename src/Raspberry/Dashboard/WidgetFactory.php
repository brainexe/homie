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
	private $_widgets;

	/**
	 * @param string $widget_id
	 * @param WidgetInterface $widget
	 */
	public function addWidget($widget_id, WidgetInterface $widget) {
		$this->_widgets[$widget_id] = $widget;
	}

	/**
	 * @param string $id
	 * @return WidgetInterface
	 * @throws InvalidArgumentException
	 */
	public function getWidget($id) {
		if (empty($this->_widgets[$id])) {
			throw new InvalidArgumentException(sprintf('Invalid widget: %s'), $id);
		}

		return $this->_widgets[$id];
	}

	/**
	 * @return string[]
	 */
	public function getAvailableWidgets() {
		return array_keys($this->_widgets);
	}

} 
