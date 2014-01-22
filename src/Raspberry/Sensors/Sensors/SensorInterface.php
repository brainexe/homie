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

	/**
	 * @param double $value
	 * @return string
	 */
	public function formatValue($value);

	/**
	 * @param float $value
	 * @return string|null
	 */
	public function getEspeakText($value);
}