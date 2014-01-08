<?php

namespace Raspberry\Traits;

use PDO;

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
	 * @param PDO $pdo
	 */
	public function setPDO(PDO $pdo) {
		$this->_pdo = $pdo;
	}

} 