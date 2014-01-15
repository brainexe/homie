<?php

namespace Raspberry\Sensors\Sensors;

class LoadSensor implements SensorInterface {

	const TYPE = 'load';

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
		return sys_getloadavg()[0];
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatValue($value) {
		return $value;
	}

}