<?php

namespace Raspberry\Sensors;

use Raspberry\Sensors\Sensors\SensorInterface;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
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
	 * @param array $sensor_data
	 * @throws \Exception
	 * @return SensorInterface
	 */
	public function build(array $sensor_data) {
		if (!empty($this->_sensors[$sensor_data['type']])) {
			return $this->_sensors[$sensor_data['type']];
		}

		throw new \Exception(sprintf('Invalid sensor type: %s', $sensor_data['type']));
	}

}