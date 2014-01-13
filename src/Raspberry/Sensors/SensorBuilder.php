<?php

namespace Raspberry\Sensors;

use Raspberry\Sensors\Sensors\LoadSensor;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\Sensors\TemperatureDS18;
use Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;

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
			case TemperatureOnBoardSensor::TYPE:
				$sensor = new TemperatureOnBoardSensor();
				break;
			case TemperatureDS18::TYPE:
				$sensor = new TemperatureDS18();
				break;
			default:
				throw new \Exception(sprintf('Invalid sensor type: %s', $sensor_data['type']));
		}

		return $sensor;
	}
} 