<?php

namespace Raspberry\Sensors\Sensors;

use Matze\Annotations\Annotations as DI;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @DI\Service(public=false, tags={{"name" = "sensor"}})
 */
class LoadSensor implements SensorInterface {

	const TYPE = 'load';

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
		return sys_getloadavg()[0];
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatValue($value) {
		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEspeakText($value) {
		return null;
	}

	public function isSupported(OutputInterface $output) {
		return true;
	}

}