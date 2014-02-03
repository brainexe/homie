<?php

namespace Raspberry\Sensors\Sensors;

use Symfony\Component\Process\Process;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false, tags={{"name" = "sensor"}})
 */
class HumidDHT11Sensor extends AbstractDHT11Sensor {

	const TYPE = 'humid_dht11';

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

		if (!preg_match('/Hum = (\d+) %/', $output, $matches)) {
			return null;
		}

		return (double)$matches[1];
	}

	/**
	 * @param double $value
	 * @return string
	 */
	public function formatValue($value) {
		return sprintf('%s%%', $value);
	}

	/**
	 * @param float $value
	 * @return string|null
	 */
	public function getEspeakText($value) {
		return sprintf('%d Prozent', $value);
	}

}