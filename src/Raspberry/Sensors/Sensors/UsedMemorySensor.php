<?php

namespace Raspberry\Sensors\Sensors;

class UsedMemorySensor implements SensorInterface {

	const TYPE = 'used_memory';

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
		// TODO: Implement getValue() method.
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatValue($value) {
	}
}