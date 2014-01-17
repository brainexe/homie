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
	 * @return integer $sensor_id
	 */
	public function addValue($sensor_id, $value) {
		$query = 'INSERT INTO sensor_values (sensor_id, value) VALUES (?, ?)';
		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$sensor_id, $value]);

		$this->invalidate(self::CACHE_KEY_LATEST);

		return $this->getPDO()->lastInsertId();
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

	/**
	 * @return double[]
	 */
	public function getLatestValue() {
		return $this->wrapCache(self::CACHE_KEY_LATEST, function() {
			$query = '
			SELECT sensor_id, value
			FROM sensor_values
			WHERE id IN (
				SELECT MAX(id)
				FROM sensor_values
				GROUP BY sensor_id
			);';

			$stm = $this->getPDO()->prepare($query);
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
		});
	}

} 