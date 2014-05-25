<?php

namespace Raspberry\Radio;

use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\MessageQueue\MessageQueueJob;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Util\TimeParser;
use Raspberry\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class RadioJob {

	use EventDispatcherTrait;

	/**
	 * @var TimeParser
	 */
	private $_time_parser;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;

	/**
	 * @Inject({"@MessageQueueGateway", "@TimeParser"})
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway, TimeParser $time_parser) {
		$this->_message_queue_gateway = $message_queue_gateway;
		$this->_time_parser = $time_parser;
	}

	/**
	 * @return MessageQueueJob[]
	 */
	public function getJobs() {
		return $this->_message_queue_gateway->getEventsByType(RadioChangeEvent::CHANGE_RADIO, time());
	}

	/**
	 * @param RadioVO $radio_vo
	 * @param string $time_string
	 * @param boolean $status
	 */
	public function addRadioJob(RadioVO $radio_vo, $time_string, $status) {
		$timestamp = $this->_time_parser->parseString($time_string);

		$event = new RadioChangeEvent($radio_vo, $status);
		$this->dispatchInBackground($event, $timestamp);
	}

	/**
	 * @param string $job_id
	 */
	public function deleteJob($job_id) {
		$this->_message_queue_gateway->deleteEvent($job_id, RadioChangeEvent::CHANGE_RADIO);
	}
}
