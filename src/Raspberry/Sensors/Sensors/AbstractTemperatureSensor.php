<?php

namespace Raspberry\Sensors\Sensors;

abstract class AbstractTemperatureSensor implements SensorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function formatValue($value) {
		return sprintf('%1.2f°', $value);
	}
} 