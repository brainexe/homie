<?php

namespace Raspberry\Gpio;

use PDO;
use Raspberry\Traits\PDOTrait;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

/**
 * @DI\Service(public=false)
 */
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