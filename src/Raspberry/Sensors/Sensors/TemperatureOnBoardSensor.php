<?php

namespace Raspberry\Sensors\Sensors;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false, tags={{"name" = "sensor"}})
 */
class TemperatureOnBoardSensor extends AbstractTemperatureSensor {

	const PATH = '/sys/class/thermal/thermal_zone0/temp';
	const TYPE = 'temperature_onboard';

	/**
	 * {@inheritdoc}
	 */
	public function getSensorType() {
		return self::TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue($pin) {
		$temp = file_get_contents(self::PATH);
		return $temp / 1000;
	}

}