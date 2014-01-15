<?php

namespace Raspberry\Sensors;

use PDO;
use Raspberry\Traits\PDOTrait;

class SensorValuesGateway {
	use PDOTrait;

	/**
	 * @param integer $sensor_id
	 * @param double $value
	 */
	public function addValue($sensor_id, $value) {
		$query = 'INSERT INTO sensor_values (sensor_id, value) VALUES (?, ?)';
		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$sensor_id, $value]);
	}

	/**
	 * @param integer $sensor_id
	 * @return array[]
	 */
	public function getSensorValues($sensor_id) {
		$query = 'SELECT *, UNIX_TIMESTAMP(timestamp) AS timestamp FROM sensor_values WHERE sensor_id = ? ORDER BY timestamp ASC';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$sensor_id]);

		return $stm->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @return double[]
	 */
	public function getLatestValue() {
		$query = '
			SELECT sensor_id, value
			FROM sensor_values
			WHERE id IN (
				SELECT max(id)
				FROM sensor_values
				GROUP BY sensor_id
			);';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute();

		return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
	}

} 