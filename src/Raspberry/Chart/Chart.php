<?php

namespace Raspberry\Chart;

/**
 * @Service(public=false)
 */
class Chart {

	const DEFAULT_TIME = 86400;

	/**
	 * @param array[] $sensors
	 * @param array[] $sensor_values
	 * @return array
	 */
	public function formatJsonData(array $sensors, array $sensor_values) {
		$output = [];

		foreach ($sensors as $sensor) {
			$sensor_id = $sensor['id'];

			$sensor_json = ['sensor_id' => $sensor_id, 'color' => '#' . substr(md5($sensor_id), 0, 6), 'name' => $sensor['name'], 'description' => $sensor['description'], 'pin' => $sensor['pin'], 'data' => []];

			if (!empty($sensor_values[$sensor_id])) {
				foreach ($sensor_values[$sensor_id] as $timestamp => $value) {
					$sensor_json['data'][] = ['x' => (int)$timestamp, 'y' => (double)$value];
				}
			} else {
				continue;
			}

			$output[] = $sensor_json;
		}

		return $output;
	}

} 