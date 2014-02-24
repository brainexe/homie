<?php

namespace Raspberry\Dashboard;
use InvalidArgumentException;

/**
 * @Service(public=false)
 */
class WidgetFactory {

	/**
	 * @var WidgetInterface
	 */
	private $_widgets;

	/**
	 * @param WidgetInterface $widget
	 */
	public function addWidget(WidgetInterface $widget) {
		$this->_widgets[$widget->getId()] = $widget;
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

} 
