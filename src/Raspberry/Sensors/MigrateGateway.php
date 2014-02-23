<?php

namespace Raspberry\Sensors;

use Matze\Core\Traits\PDOTrait;
use Matze\Core\Traits\RedisTrait;
use PDO;

/**
 * @Service
 */
class MigrateGateway {

	use RedisTrait;
	use PDOTrait;

	/**
	 * @var SensorGateway
	 */
	private $_sensor_gateway;

	/**
	 * @Inject("@SensorGateway")
	 */
	function __construct(SensorGateway $sensor_gateway) {
		$this->_sensor_gateway = $sensor_gateway;
	}

	public function migrateSensors() {
		$predis = $this->getPredis();
		$pdo = $this->getPDO();

		$query = 'SELECT * FROM sensors ORDER BY name';

		$stm = $pdo->prepare($query);
		$stm->execute();

		$sensors  = $stm->fetchAll(\PDO::FETCH_ASSOC);

		foreach ($sensors as $sensor) {
			print_r($sensor);

			$old_sensor_id = $sensor['id'];
			$sensor_id = $this->_sensor_gateway->addSensor($sensor['name'], $sensor['type'], $sensor['description'], $sensor['pin'], $sensor['interval']);

			$query = "SELECT *, UNIX_TIMESTAMP(timestamp) AS timestamp
			FROM sensor_values
			WHERE sensor_id = ?
			ORDER BY timestamp ASC";

			$stm = $this->getPDO()->prepare($query);
			$stm->execute([$old_sensor_id]);

			$sensor_values = $stm->fetchAll(PDO::FETCH_ASSOC);

			foreach ($sensor_values as $sensor_value) {
				echo ".";
				$key = SensorValuesGateway::SENSOR_VALUES_PREFIX . $sensor_id;
				$predis->ZADD($key, $sensor_value['timestamp'], $sensor_value['timestamp'].'-'.$sensor_value['value']);
			}
		}
	}
}