<?php

namespace Raspberry\Radio;

use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\RedisTrait;
use Matze\Core\Util\TimeParser;
use Raspberry\Radio\VO\RadioVO;

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
	 * @var TimeParser
	 */
	private $_time_parser;

	/**
	 * @Inject({"@RadioJobGateway", "@TimeParser"})
	 */
	public function __construct(RadioJobGateway $radio_job_gateway, TimeParser $time_parser) {
		$this->_radio_job_gateway = $radio_job_gateway;
		$this->_time_parser = $time_parser;
	}

	/**
	 * @return array[]
	 */
	public function getJobs() {
		return $this->_radio_job_gateway->getJobs();
	}

	/**
	 * @param RadioVO $radio_vo
	 * @param string $time_string
	 * @param boolean $status
	 */
	public function addRadioJob(RadioVO $radio_vo, $time_string, $status) {
		$timestamp = $this->_time_parser->parseString($time_string);

		$event = new RadioChangeEvent($radio_vo, $status, true);
		$this->dispatchInBackground($event, $timestamp);

		$this->_radio_job_gateway->addRadioJob($radio_vo->id, $timestamp, $status);
	}

	/**
	 * @param integer $radio_id
	 * @param integer $status
	 */
	public function deleteJob($radio_id, $status) {
		$this->_radio_job_gateway->deleteJob($radio_id, $status);
	}
}
