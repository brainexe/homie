<?php

namespace Raspberry\Sensors;

use Matze\Core\Traits\RedisTrait;

/**
 * @codeCoverageIgnore
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

		$redis = $this->getPredis()->pipeline();
		foreach ($sensor_ids as $sensor_id) {
			$redis->HGETALL($this->_getKey($sensor_id));
		}

		return $redis->execute();
	}

	/**
	 * @return integer[]
	 */
	public function getSensorIds() {
		$sensor_ids = $this->getPredis()->SMEMBERS(self::SENSOR_IDS);

		sort($sensor_ids);

		return $sensor_ids;
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param string $description
	 * @param integer $pin
	 * @param integer $interval
	 * @return integer
	 */
	public function addSensor($name, $type, $description, $pin, $interval) {
		$sensor_ids = $this->getSensorIds();
		$new_sensor_id = end($sensor_ids) + 1;

		$predis = $this->getPredis()->pipeline();

		$key = $this->_getKey($new_sensor_id);
		$predis->HMSET($key, [
			'id' => $new_sensor_id,
			'name' => $name,
			'type' => $type,
			'description' => $description,
			'pin' => $pin,
			'interval' => $interval,
			'last_value' => 0,
			'last_value_timestamp' => 0
		]);

		$this->getPredis()->SADD(self::SENSOR_IDS, $new_sensor_id);

		$predis->execute();

		return $new_sensor_id;
	}

	/**
	 * @param integer $sensor_id
	 * @return array
	 */
	public function getSensor($sensor_id) {
		$key = $this->_getKey($sensor_id);

		return $this->getPredis()->HGETALL($key);
	}

	/**
	 * @param integer $sensor_id
	 */
	public function deleteSensor($sensor_id) {
		$redis = $this->getPredis();

		$redis->DEL($this->_getKey($sensor_id));
		$redis->SREM(self::SENSOR_IDS, $sensor_id);
		$redis->DEF(sprintf(SensorValuesGateway::REDIS_SENSOR_VALUES, $sensor_id));
	}

	/**
	 * @param integer $sensor_id
	 * @return string
	 */
	private function _getKey($sensor_id) {
		return self::REDIS_SENSOR_PREFIX . $sensor_id;
	}
} 