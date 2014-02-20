<?php

namespace Raspberry\Sensors\Sensors;

use Matze\Annotations\Annotations as DI;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @DI\Service(public=false, tags={{"name" = "sensor"}})
 */
class TemperatureOnBoardSensor extends AbstractTemperatureSensor {

	const PATH = '/sys/class/thermal/thermal_zone0/temp';
	const TYPE = 'temperature_onboard';

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
		$temp = file_get_contents(self::PATH);
		return $temp / 1000;
	}

	public function isSupported(OutputInterface $output) {
		if (!is_file(self::PATH)) {
			$output->writeln(sprintf('<error>%s: Thermal zone file does not exist: %s</error>', self::getSensorType(), self::PATH));
			return false;
		}

		return true;
	}

}