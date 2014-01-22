<?php

namespace Raspberry\Sensors\Sensors;

abstract class AbstractTemperatureSensor implements SensorInterface {

	/**
	 * {@inheritdoc}
	 */
	public function formatValue($value) {
		return sprintf('%1.2f°', $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEspeakText($value) {
		return str_replace('.', ',', sprintf('Es ist %0.1f Grad warm.', $value));
	}
} 