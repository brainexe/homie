<?php

namespace Raspberry\Sensors;

use PDO;
use Raspberry\Traits\PDOTrait;
use Raspberry\Traits\RedisCacheTrait;

class SensorValuesGateway {
	use PDOTrait;
	use RedisCacheTrait;

	const CACHE_KEY_LATEST = 'sensor_values';

	/**
	 * @param integer $sensor_id
	 * @param double $value
	 */
	public function addValue($sensor_id, $value) {
		$query = 'INSERT INTO sensor_values (sensor_id, value) VALUES (?, ?)';
		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$sensor_id, $value]);

		$query = 'UPDATE sensors SET last_value = ? WHERE id = ?';
		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$value, $sensor_id]);
	}

	/**
	 * @param integer $sensor_id
	 * @param integer $from
	 * @return array[]
	 */
	public function getSensorValues($sensor_id, $from) {
		$query = '
			SELECT *, UNIX_TIMESTAMP(timestamp) AS timestamp
			FROM sensor_values
			WHERE sensor_id = ?
			AND timestamp >= FROM_UNIXTIME(?)
			ORDER BY timestamp ASC
		';

		if ($from) {
			$from = time() - $from;
		}

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$sensor_id, $from]);

		return $stm->fetchAll(PDO::FETCH_ASSOC);
	}

} 