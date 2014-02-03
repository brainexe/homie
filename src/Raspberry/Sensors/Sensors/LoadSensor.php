<?php

namespace Raspberry\Sensors\Sensors;

use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

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

}