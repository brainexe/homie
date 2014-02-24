<?php

namespace Raspberry\Sensors\Sensors;

use Matze\Core\Traits\TranslatorTrait;

abstract class AbstractTemperatureSensor implements SensorInterface {

	use TranslatorTrait;

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
		return str_replace('.', ',', sprintf($this->trans('%0.1f Degree'), $value));
	}

} 