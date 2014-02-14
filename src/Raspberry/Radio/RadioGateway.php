<?php

namespace Raspberry\Radio;

use Matze\Core\Traits\PDOTrait;
use PDO;
use Matze\Annotations\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
class RadioGateway {
	use PDOTrait;

	/**
	 * @return array[]
	 */
	public function getRadios() {
		$query = 'SELECT * from radio ORDER BY name';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute();

		return $stm->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * @param string $name
	 * @param string $description
	 * @param string $pin
	 */
	public function addRadio($name, $description, $pin) {
		$query = 'INSERT INTO sensors (name, description, pin) VALUES (?, ?, ?)';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$name, $description, $pin]);
	}
} 