<?php

namespace Raspberry\Twig\Extensions;

use Twig_Extension;

/**
 * @TwigExtension
 */
class SensorExtension extends Twig_Extension {

	/**
	 * {@inheritdoc}
	 */
	function getName() {
		return 'raspberry_sensors';
	}

	public function getFilters() {
		return ['sensor' => new \Twig_Filter_Method($this, 'sensorsFilter'),];
	}

	/**
	 * @param integer $sensor_id
	 * @param array $available_sensors
	 * @return string
	 */
	public function sensorsFilter($sensor_id, array $available_sensors) {
		if (($key = array_search($sensor_id, $available_sensors)) !== false) {
			unset($available_sensors[$key]);
		} else {
			$available_sensors[] = $sensor_id;
		}

		sort($available_sensors);

		return implode(':', $available_sensors);
	}

}