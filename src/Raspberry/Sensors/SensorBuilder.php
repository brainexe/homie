<?php

namespace Raspberry\Sensors;

use Raspberry\Sensors\Sensors\HumidDHT11Sensor;
use Raspberry\Sensors\Sensors\LoadSensor;
use Raspberry\Sensors\Sensors\SensorInterface;
use Raspberry\Sensors\Sensors\TemperatureDHT11Sensor;
use Raspberry\Sensors\Sensors\TemperatureDS18;
use Raspberry\Sensors\Sensors\TemperatureOnBoardSensor;
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