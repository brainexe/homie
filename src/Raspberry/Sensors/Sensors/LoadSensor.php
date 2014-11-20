<?php

namespace Raspberry\Sensors\Sensors;


use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Service(public=false, tags={{"name" = "sensor"}})
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
		return sprintf('%0.1f', $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEspeakText($value) {
		return sprintf('%1.2f', $value);
	}


	/**
	 * {@inheritdoc}
	 */
	public function isSupported(OutputInterface $output) {
		return true;
	}

}