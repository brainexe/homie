<?php

namespace Raspberry\Sensors\Sensors;

trait TemperatureSensorTrait {

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
		return str_replace('.', ',', sprintf('%0.1f Degree', $value));
	}

}