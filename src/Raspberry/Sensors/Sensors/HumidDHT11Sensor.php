<?php

namespace Raspberry\Sensors\Sensors;

use Symfony\Component\Process\Process;

class HumidDHT11Sensor implements SensorInterface {

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
		$command = sprintf('sudo /opt/Adafruit-Raspberry-Pi-Python-Code/Adafruit_DHT_Driver/Adafruit_DHT 11 %d', $pin);

		$process = new Process($command);
		$process->run();

		if ($process->isSuccessful()) {
			$output = $process->getOutput();
			if (strpos($output, 'Hum') === false) {
				return null;
			}

			if (preg_match('/Hum = (\d+) %/', $output, $matches)) {
				return (double)$matches[1];
			}
		}

		return null;
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
		return sprintf('Luftfeuchtigkeit %d%%', $value);
	}
}