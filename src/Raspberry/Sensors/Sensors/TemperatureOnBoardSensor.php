<?php

namespace Raspberry\Sensors\Sensors;

class TemperatureOnBoardSensor implements SensorInterface {

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