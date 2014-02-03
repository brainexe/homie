<?php

namespace Raspberry\Sensors;

use PDO;
use Raspberry\Traits\PDOTrait;
use Raspberry\Traits\RedisCacheTrait;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
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

	/**
	 * @param integer $days
	 * @param integer $deleted_percent
	 */
	public function deleteOldValues($days, $deleted_percent) {
		$query = '
			DELETE FROM sensor_values
			WHERE (crc32(MD5(id)) % 100 < ?)
			AND timestamp < (DATE_SUB(NOW(), INTERVAL ? DAY));
		';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$deleted_percent, $days]);
	}

} 