<?php

namespace Raspberry\Sensors;

use PDO;
use Raspberry\Traits\PDOTrait;

class SensorGateway {
	use PDOTrait;

	/**
	 * @return array[]
	 */
	public function getSensors() {
		$query = 'SELECT * from sensors ORDER BY name';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute();

		return $stm->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @param string $name
	 * @param string $type
	 * @param string $description
	 * @param integer $pin
	 * @param integer $interval
	 */
	public function addSensor($name, $type, $description, $pin, $interval) {
		$query = 'INSERT INTO sensors (name, type, description, pin, `interval`) VALUES (?, ?, ?, ?, ?)';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$name, $type, $description, $pin, $interval]);
	}

	/**
	 * @param integer $sensor_id
	 * @return array
	 */
	public function getSensor($sensor_id) {
		$query = 'SELECT * from sensors WHERE id = ?';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$sensor_id]);

		return $stm->fetch(PDO::FETCH_ASSOC);
	}
} 