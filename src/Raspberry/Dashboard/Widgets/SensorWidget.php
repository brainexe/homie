<?php

namespace Raspberry\Dashboard\Widgets;

use Raspberry\Dashboard\AbstractWidget;
use Raspberry\Sensors\Sensors\SensorInterface;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class SensorWidget extends AbstractWidget {

	const TYPE = 'sensor';

	/**
	 * @var SensorInterface
	 */
	private $_sensor;

	/**
	 * @var array
	 */
	private $_sensor_data;

	/**
	 * @return string
	 */
	public function render() {
		return $this->_sensor->formatValue($this->_sensor_data['last_value']);
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return sprintf('%s (%s)', $this->_sensor_data['name'], $this->_sensor_data['description']);
	}

	/**
	 * @return string
	 */
	public function getId() {
		return self::TYPE;
	}

	/**
	 * @param array $payload
	 */
	public function create(array $payload) {
		// TODO set payload
	}
}
