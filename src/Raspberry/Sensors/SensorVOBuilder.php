<?php

namespace Raspberry\Sensors;

/**
 * @Service(public=false)
 */
class SensorVOBuilder {

	/**
	 * @param array $data
	 * @return SensorVO
	 */
	public function buildFromArray(array $data) {
		return $this->build($data['id'], $data['name'], $data['description'], $data['interval'], $data['node'], $data['pin'], $data['type'], $data['last_value'], $data['last_value_timestamp']);
	}

	/**
	 * @param int $id
	 * @param string $name
	 * @param string $description
	 * @param int $interval
	 * @param int $node
	 * @param string $pin
	 * @param string $type
	 * @param float $last_value
	 * @param int $last_value_timestamp
	 * @return SensorVO
	 */
	public function build($id, $name, $description, $interval, $node, $pin, $type, $last_value, $last_value_timestamp) {
		$sensor = new SensorVO();

		$sensor->id = $id;
		$sensor->name = $name;
		$sensor->description = $description;
		$sensor->interval = $interval;
		$sensor->node = $node;
		$sensor->pin = $pin;
		$sensor->type = $type;
		$sensor->last_value = $last_value;
		$sensor->last_value_timestamp = $last_value_timestamp;

		return $sensor;
	}

} 