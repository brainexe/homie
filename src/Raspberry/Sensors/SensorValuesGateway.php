<?php

namespace Raspberry\Sensors;

use Matze\Core\Traits\RedisCacheTrait;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class SensorValuesGateway {

	const SENSOR_VALUES_PREFIX = 'sensor_values:';

	use RedisTrait;

	/**
	 * @param integer $sensor_id
	 * @param double $value
	 */
	public function addValue($sensor_id, $value) {
		$predis = $this->getPredis();

		$key = self::SENSOR_VALUES_PREFIX . $sensor_id;
		$predis->ZADD($key, time(), time().'-'.$value);

		$predis->HSET(SensorGateway::SENSOR_PREFIX . $sensor_id, 'last_value', $value);
	}

	/**
	 * @param integer $sensor_id
	 * @param integer $from
	 * @return array[]
	 */
	public function getSensorValues($sensor_id, $from) {
		if ($from) {
			$from = time() - $from;
		}

		$key = self::SENSOR_VALUES_PREFIX . $sensor_id;
		$redis_result = $this->getPredis()->ZRANGEBYSCORE($key, $from, time(), 'WITHSCORES');
		$result = [];

		foreach ($redis_result as $result) {
			$result[$result[1]] = explode('-', $result[0])[1];
		}

		return $result;
	}

	/**
	 * @param integer $days
	 * @param integer $deleted_percent
	 */
	public function deleteOldValues($days, $deleted_percent) {
		return;
		//TODO
		$query = '
			DELETE FROM sensor_values
			WHERE (crc32(MD5(id)) % 100 < ?)
			AND timestamp < (DATE_SUB(NOW(), INTERVAL ? DAY));
		';
	}

} 