<?php

namespace Raspberry\Gpio;

use PDO;
use Raspberry\Traits\PDOTrait;

class PinGateway {
	use PDOTrait;

	/**
	 * @return array[]
	 */
	public function getPinDescriptions() {
		$query = 'SELECT id, description FROM pins';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute();

		return $stm->fetchAll(PDO::FETCH_KEY_PAIR);
	}

} 