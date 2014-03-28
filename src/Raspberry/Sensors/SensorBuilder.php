<?php

namespace Raspberry\Sensors;

use InvalidArgumentException;
use Raspberry\Sensors\Sensors\SensorInterface;


/**
 * @Service(public=false)
 */
class SensorBuilder {

	/**
	 * @var SensorInterface[]
	 */
	private $_sensors;

	/**
	 * @return SensorInterface[]
	 */
	public function getSensors() {
		return $this->_sensors;
	}

	/**
	 * @param string $type
	 * @param SensorInterface $sensor
	 */
	public function addSensor($type, SensorInterface $sensor) {
		$this->_sensors[$type] = $sensor;
	}

	/**
	 * @param string $sensor_type
	 * @throws InvalidArgumentException
	 * @return SensorInterface
	 */
	public function build($sensor_type) {
		if (!empty($this->_sensors[$sensor_type])) {
			return $this->_sensors[$sensor_type];
		}

		throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $sensor_type));
	}

}