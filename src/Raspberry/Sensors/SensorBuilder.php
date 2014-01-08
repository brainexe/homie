<?php

namespace Raspberry\Sensors;

use Raspberry\Sensors\Sensors\LoadSensor;
use Raspberry\Sensors\Sensors\SensorInterface;

class SensorBuilder {
	/**
	 * @param array $sensor_data
	 * @throws \Exception
	 * @return SensorInterface
	 */
	public function build(array $sensor_data) {
		switch ($sensor_data['type']) {
			case LoadSensor::TYPE:
				$sensor = new LoadSensor();
				break;
			default:
				throw new \Exception(sprintf('Invalid sensor type: %s', $sensor_data['type']));
		}

		return $sensor;
	}
} 