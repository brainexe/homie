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
	 * @param WidgetInterface $widget
	 */
	public function addWidget(WidgetInterface $widget) {
		$this->widgets[$widget->getId()] = $widget;
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
	 * @return WidgetInterface[]
	 */
	public function getAvailableWidgets() {
		return array_values($this->widgets);
	}

}
