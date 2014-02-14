<?php

namespace Raspberry\Sensors\Sensors;

use Symfony\Component\Process\Process;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false, tags={{"name" = "sensor"}})
 */
class TemperatureDHT11Sensor extends AbstractDHT11Sensor {

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
		$output = $this->getContent($pin);

		if (!$output) {
			return null;
		}

		if (!preg_match('/Temp = (\d+) /', $output, $matches)) {
			return null;
		}

		return (double)$matches[1];
	}

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
		return str_replace('.', ',', sprintf('%0.1f Grad', $value));
	}
}