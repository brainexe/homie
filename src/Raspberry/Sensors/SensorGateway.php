<?php

namespace Raspberry\Sensors;

use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class SensorGateway {

	const SENSOR_PREFIX = 'sensor:';
	const SENSOR_IDS = 'sensor_ids';

	use RedisTrait;

	/**
	 * @return array[]
	 */
	public function getSensors() {
		$sensor_ids = $this->getSensorIds();

		$sensors = [];
		$redis = $this->getPredis();
		foreach ($sensor_ids as $sensor_id) {
			$sensors[str_replace(self::SENSOR_PREFIX, '', $sensor_id)] = $redis->HGETALL($this->_getKey($sensor_id));
		}

		return $sensors;
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

		$sensor_id = end($sensor_ids) + 1;

		$this->getPredis()->HMSET(self::SENSOR_PREFIX.$sensor_id, [
			'id' => $sensor_id,
			'name' => $name,
			'type' => $type,
			'description' => $description,
			'pin' => $pin,
			'interval' => $interval,
			'last_value' => 0
		]);

		$this->getPredis()->SADD(self::SENSOR_IDS, $sensor_id);

		return $sensor_id;
	}

	/**
	 * @param integer $sensor_id
	 * @return array
	 */
	public function getSensor($sensor_id) {
		$key = self::SENSOR_PREFIX . $sensor_id;

		return $this->getPredis()->HGETALL($key);
	}

	/**
	 * @param integer $sensor_id
	 * @return string
	 */
	private function _getKey($sensor_id) {
		return self::SENSOR_PREFIX . $sensor_id;
	}
} 