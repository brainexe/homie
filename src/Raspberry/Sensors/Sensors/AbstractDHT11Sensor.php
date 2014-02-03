<?php

namespace Raspberry\Sensors\Sensors;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractDHT11Sensor implements SensorInterface {

	const ADA_SCRIPT = '/opt/Adafruit-Raspberry-Pi-Python-Code/Adafruit_DHT_Driver/Adafruit_DHT';

	/**
	 * @param integer $pin
	 * @return null|string
	 */
	protected function getContent($pin) {
		$command = sprintf('sudo %s 11 %d', self::ADA_SCRIPT, $pin);

		$process = new Process($command);
		$process->run();

		if (!$process->isSuccessful()) {
			return null;
		}

		return $process->getOutput();
	}

	public function isSupported(OutputInterface $output) {
		if (!is_file(self::ADA_SCRIPT)) {
			$output->writeln(sprintf('<error>%s: ada script not exists: %s</error>', $this->getSensorType(), self::ADA_SCRIPT));
			return false;
		}

		return true;
	}

} 