<?php

namespace Raspberry\Sensors\Sensors;

class TemperatureDHT11Sensor extends AbstractTemperatureSensor {

	const TYPE =  'temp_dht11';

	/**
	 * @return string
	 */
	public function getSensorType() {
		return self::TYPE;
	}

	/**
	 * @param integer $pin
	 * @return double
	 */
	public function getValue($pin) {
		// TODO: Implement getValue() method.
	}
}