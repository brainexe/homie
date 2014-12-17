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
	private $sensors;

	/**
	 * @return SensorInterface[]
	 */
	public function getSensors() {
		return $this->sensors;
	}

	/**
	 * @param string $type
	 * @param SensorInterface $sensor
	 */
	public function addSensor($type, SensorInterface $sensor) {
		$this->sensors[$type] = $sensor;
	}

	/**
	 * @param string $sensor_type
	 * @throws InvalidArgumentException
	 * @return SensorInterface
	 */
	public function build($sensor_type) {
		if (!empty($this->sensors[$sensor_type])) {
			return $this->sensors[$sensor_type];
		}

		throw new InvalidArgumentException(sprintf('Invalid sensor type: %s', $sensor_type));
	}

}
