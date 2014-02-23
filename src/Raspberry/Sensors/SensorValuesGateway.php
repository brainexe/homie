<?php

namespace Raspberry\Sensors;

use Matze\Core\Traits\RedisCacheTrait;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class SensorValuesGateway {

	const REDIS_SENSOR_VALUES = 'sensor_values:%d';

	use RedisTrait;

	/**
	 * @param integer $sensor_id
	 * @param double $value
	 */
	public function addValue($sensor_id, $value) {
		$predis = $this->getPredis()->pipeline();

		$key = $this->_getKey($sensor_id);
		$predis->ZADD($key, time(), time().'-'.$value);

		$predis->HMSET(SensorGateway::REDIS_SENSOR_PREFIX . $sensor_id, [
			'last_value' => $value,
			'last_value_timestamp' => time()
		]);

		$predis->execute();
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

		$key = $this->_getKey($sensor_id);
		$redis_result = $this->getPredis()->ZRANGEBYSCORE($key, $from, time());
		$result = [];

		foreach ($redis_result as $part) {
			list($timestamp, $value) = explode('-', $part);
			$result[$timestamp] = $value;
		}

		return $result;
	}

	/**
	 * @param integer $sensor_id
	 * @param integer $days
	 * @param integer $deleted_percent
	 * @return integer $deleted_rows
	 */
	public function deleteOldValues($sensor_id, $days, $deleted_percent) {
		$deleted = 0;

		$predis = $this->getPredis();

		$until_timestamp = time() - $days * 86000;
		$key = $this->_getKey($sensor_id);
		$old_sensor_values = $predis->ZRANGEBYSCORE($key, 0, $until_timestamp);

		foreach ($old_sensor_values as $result) {
			$crc_32 = crc32(md5($result));

			if ($crc_32 % 100 < $deleted_percent) {
				$predis->ZREM($key, $result);

				$deleted += 1;
			}
		}

		return $deleted;
	}

	/**
	 * @param integer $sensor_id
	 * @return string
	 */
	private function _getKey($sensor_id) {
		return sprintf(self::REDIS_SENSOR_VALUES, $sensor_id);
	}

} 