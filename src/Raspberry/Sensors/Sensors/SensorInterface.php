<?php

namespace Raspberry\Sensors\Sensors;

interface SensorInterface {

	/**
	 * @return string
	 */
	public function getSensorType();

	/**
	 * @param integer $pin
	 * @return double
	 */
	public function getValue($pin);

} 