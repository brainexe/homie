<?php

namespace Raspberry\Dashboard;

use Raspberry\Sensors\Sensors\SensorInterface;

/**
 * @Widget
 */
class TemperatureWidget extends AbstractWidget {

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
	 * @param array $payload
	 */
	public function create(array $payload) {

	}
}
