<?php

namespace Raspberry\Radio;

use Matze\Core\EventDispatcher\MessageQueueEvent;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class RadioJob {

	use EventDispatcherTrait;

	/**
	 * @var RadioJobGateway
	 */
	private $_radio_job_gateway;

	/**
	 * @var Radios;
	 */
	private $_radios;

	/**
	 * @Inject({"@RadioJobGateway", "@Radios"})
	 */
	public function __construct(RadioJobGateway $radio_job_gateway, Radios $radios) {
		$this->_radio_job_gateway = $radio_job_gateway;
		$this->_radios = $radios;
	}

	/**
	 * @return array[]
	 */
	public function getJobs() {
		return $this->_radio_job_gateway->getJobs();
	}

	/**
	 * @param integer $radio_id
	 * @param integer $timestamp
	 * @param integer $status
	 */
	public function addRadioJob($radio_id, $timestamp, $status) {
		$this->_radio_job_gateway->addRadioJob($radio_id, $timestamp, $status);
	}

	public function handlePendingJobs() {
		$pending_jobs = $this->_radio_job_gateway->getPendingJobs(time());

		foreach ($pending_jobs as $pending_job) {
			$radio = $this->_radios->getRadio($pending_job['radio_id']);

			$event = new MessageQueueEvent('RadioController', 'setStatus', [$radio['code'], $radio['pin'], $pending_job['status']]);
			$this->getEventDispatcher()->dispatch(MessageQueueEvent::NAME, $event);

			$this->_radio_job_gateway->deleteJob(sprintf('%s-%s', $pending_job['radio_id'], $pending_job['status']));
		}
	}

	/**
	 * @param string $job_id
	 */
	public function deleteJob($job_id) {
		$this->_radio_job_gateway->deleteJob($job_id);
	}
}
