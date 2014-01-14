<?php

namespace Raspberry\Sensors\Sensors;

class TemperatureDS18 implements SensorInterface {

	const TYPE = 'temp_ds18';

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
		$path = sprintf('/sys/bus/w1/devices/%s/w1_slave', $pin);
		$content = file_get_contents($path);

		if (strpos($content, 'YES') === false) {
			// invalid response :(
			return null;
		}

		$matches = null;
		if (!preg_match('/t=(\d+)$/', $content, $matches)) {
			return null;
		}

		return $matches[1]/1000;
	}
}