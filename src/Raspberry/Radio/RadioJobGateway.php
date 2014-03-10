<?php

namespace Raspberry\Radio;

use Matze\Core\Traits\RedisTrait;

/**
 * @codeCoverageIgnore
 * @Service
 */
class RadioJobGateway {

	use RedisTrait;

	const REDIS_QUEUE = 'radio_jobs';

	/**
	 * @return array[]
	 */
	public function getJobs() {
		return $this->_getJobs('0', '+inf');
	}

	/**
	 * @param integer $now
	 * @return array[]
	 */
	public function getPendingJobs($now) {
		return $this->_getJobs('0', $now);
	}

	/**
	 * @param integer $from
	 * @param integer $to
	 * @return array[]
	 */
	private function _getJobs($from, $to) {
		$jobs = [];
		$now = time();

		$redis_result = $this->getPredis()->ZRANGEBYSCORE(self::REDIS_QUEUE, $from, $to, 'WITHSCORES');
		foreach ($redis_result as $job) {
			list ($radio_id, $status) = explode('-', $job[0]);
			$timestamp = $job[1];

			$jobs[] = [
				'radio_id' => $radio_id,
				'status' => $status,
				'timestamp' => $timestamp,
				'eta' => $timestamp - $now,
			];
		}

		return $jobs;
	}

	/**
	 * @param integer $radio_id
	 * @param integer $timestamp
	 * @param string $status
	 */
	public function addRadioJob($radio_id, $timestamp, $status) {
		$predis = $this->getPredis();

		$redis_member = sprintf('%d-%d', $radio_id, $status);

		$predis->ZADD(self::REDIS_QUEUE, $timestamp, $redis_member);
	}

	/**
	 * @param integer $radio_id
	 * @param boolean $status
	 */
	public function deleteJob($radio_id, $status) {
		$job_id = $this->_getJobId($radio_id, $status);

		$this->getPredis()->ZREM(self::REDIS_QUEUE, $job_id);
	}

	/**
	 * @param integer $radio_id
	 * @param boolean $status
	 * @return string
	 */
	private function _getJobId($radio_id, $status) {
		return sprintf('%d-%d', $radio_id, $status);
	}
}
