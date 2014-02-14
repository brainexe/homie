<?php

namespace Raspberry\Radio;

use Matze\Annotations\Annotations as DI;
use Matze\Core\Traits\PDOTrait;

/**
 * @DI\Service(public=false)
 */
class RadioJobGateway {
	use PDOTrait;

	/**
	 * @return array[]
	 */
	public function getJobs() {
		$query = 'SELECT * FROM radio_job';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute();

		return $stm->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @param integer $timestamp
	 * @return array[]
	 */
	public function getPendingJobs($timestamp) {
		$query = '
			SELECT r.*, j.status
			FROM radio_job AS j
			JOIN radio AS r ON (j.radio_id = j.id)
			WHERE j.time >= ?
			';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$timestamp]);

		return $stm->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @param integer $radio_id
	 * @param integer $timestamp
	 * @param string $status
	 */
	public function addRadioJob($radio_id, $timestamp, $status) {
		$query = 'INSERT INTO radio_job (radio_id, time, status) VALUES (?, ?, ?)';

		$stm = $this->getPDO()->prepare($query);
		$stm->execute([$radio_id, $timestamp, $status]);
	}
} 