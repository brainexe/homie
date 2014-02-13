<?php

namespace Matze\Core\Traits;

use PDO;
use Loso\Bundle\DiAnnotationsBundle\DependencyInjection\Annotations as DI;

trait PDOTrait {

	/**
	 * @var PDO
	 */
	private $_pdo;

	/**
	 * @return PDO
	 */
	public function getPDO() {
		return $this->_pdo;
	}

	/**
	 * @DI\Inject("@PDO")
	 */
	public function setPDO(PDO $pdo) {
		$this->_pdo = $pdo;
	}

} 