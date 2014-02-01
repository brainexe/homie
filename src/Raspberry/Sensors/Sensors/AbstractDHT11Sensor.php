<?php

namespace Raspberry\Sensors\Sensors;

use Symfony\Component\Process\Process;

abstract class AbstractDHT11Sensor implements SensorInterface {

	/**
	 * @param integer $pin
	 * @return null|string
	 */
	protected function getContent($pin) {
		$command = sprintf('sudo /opt/Adafruit-Raspberry-Pi-Python-Code/Adafruit_DHT_Driver/Adafruit_DHT 11 %d', $pin);

		$process = new Process($command);
		$process->run();

		if (!$process->isSuccessful()) {
			return null;
		}

		return $process->getOutput();
	}

} 