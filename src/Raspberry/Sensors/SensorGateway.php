<?php

namespace Raspberry\Sensors;

use BrainExe\Core\Traits\RedisTrait;
use Redis;

/**
 * @Service(public=false)
 */
class SensorGateway {

	const REDIS_SENSOR_PREFIX = 'sensor:';
	const SENSOR_IDS = 'sensor_ids';

	use RedisTrait;

	/**
	 * @return array[]
	 */
	public function getSensors() {
		$sensor_ids = $this->getSensorIds();

		$redis = $this->getRedis()->multi(Redis::PIPELINE);
		foreach ($sensor_ids as $sensor_id) {
			$redis->HGETALL($this->_getKey($sensor_id));
		}

		return $redis->exec();
	}

	/**
	 * @param integer $node_id
	 * @return array[]
	 */
	public function getSensorsForNode($node_id) {
		$sensors = $this->getSensors();

		return array_filter($sensors, function($sensor) use($node_id) {
			return $sensor['node'] == $node_id;
		});
	}

	/**
	 * @return integer[]
	 */
	public function getSensorIds() {
		$sensor_ids = $this->getRedis()->sMembers(self::SENSOR_IDS);

		sort($sensor_ids);

		return $sensor_ids;
	}

	/**
	 * @param SensorVO $sensor_vo
	 * @return integer
	 */
	public function addSensor(SensorVO $sensor_vo) {
		$sensor_ids = $this->getSensorIds();
		$new_sensor_id = end($sensor_ids) + 1;

		$redis = $this->getRedis()->multi(Redis::PIPELINE);

		$key = $this->_getKey($new_sensor_id);

		$sensor_data = (array)$sensor_vo;
		$sensor_data['id'] = $new_sensor_id;
		$sensor_data['last_value'] = 0;
		$sensor_data['last_value_timestamp'] = 0;

		$redis->HMSET($key, $sensor_data);

		$redis->sAdd(self::SENSOR_IDS, $new_sensor_id);

		$redis->exec();

		$sensor_vo->id = $new_sensor_id;

		return $new_sensor_id;
	}

	/**
	 * @param integer $sensor_id
	 * @return array
	 */
	public function getSensor($sensor_id) {
		$key = $this->_getKey($sensor_id);

		return $this->getRedis()->hGetAll($key);
	}

	/**
	 * @param integer $sensor_id
	 */
	public function deleteSensor($sensor_id) {
		$redis = $this->getRedis();

		$redis->del($this->_getKey($sensor_id));
		$redis->sRem(self::SENSOR_IDS, $sensor_id);
		$redis->del(sprintf(SensorValuesGateway::REDIS_SENSOR_VALUES, $sensor_id));
	}

	/**
	 * @param integer $sensor_id
	 * @return string
	 */
	private function _getKey($sensor_id) {
		return self::REDIS_SENSOR_PREFIX . $sensor_id;
	}
} 