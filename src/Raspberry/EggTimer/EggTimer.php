<?php

namespace Raspberry\EggTimer;

use Matze\Core\Application\UserException;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\MessageQueue\MessageQueueJob;
use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Util\TimeParser;
use Raspberry\Espeak\EspeakVO;

/**
 * @Service(public=false)
 */
class EggTimer {

	const EGG_TIMER_RING_SOUND = 'assets/sounds/egg_timer.mp3';

	use EventDispatcherTrait;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;
	/**
	 * @var TimeParser
	 */
	private $_time_parser;

	/**
	 * @Inject({"@MessageQueueGateway", "@TimeParser"})
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway, TimeParser $time_parser) {
		$this->_message_queue_gateway = $message_queue_gateway;
		$this->_time_parser = $time_parser;
	}

	/**
	 * @param string $time
	 * @param string $text
	 * @throws UserException
	 */
	public function addNewJob($time, $text) {
		if ($text) {
			$espeak_vo = new EspeakVO($text);
		} else {
			$espeak_vo = null;
		}

		$event = new EggTimerEvent($espeak_vo);

		$timestamp = $this->_time_parser->parseString($time);

		$this->dispatchInBackground($event, $timestamp);
	}

	/**
	 * @param string $job_id
	 */
	public function deleteJob($job_id) {
		$this->_message_queue_gateway->deleteEvent($job_id, EggTimerEvent::DONE);
	}

	/**
	 * @return MessageQueueJob[]
	 */
	public function getJobs() {
		return $this->_message_queue_gateway->getEventsByType(EggTimerEvent::DONE, time());
	}
} 