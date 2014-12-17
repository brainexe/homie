<?php

namespace Raspberry\EggTimer;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Traits\TimeTrait;
use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\MessageQueue\MessageQueueJob;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Util\TimeParser;
use Raspberry\Espeak\EspeakVO;

/**
 * @Service(public=false)
 */
class EggTimer {

	const EGG_TIMER_RING_SOUND = 'assets/sounds/egg_timer.mp3';

	use TimeTrait;
	use EventDispatcherTrait;

	/**
	 * @var MessageQueueGateway
	 */
	private $messageQueueGateway;

	/**
	 * @var TimeParser
	 */
	private $timeParser;

	/**
	 * @Inject({"@MessageQueueGateway", "@TimeParser"})
	 * @param MessageQueueGateway $message_queue_gateway
	 * @param TimeParser $time_parser
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway, TimeParser $time_parser) {
		$this->messageQueueGateway = $message_queue_gateway;
		$this->timeParser = $time_parser;
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

		$timestamp = $this->timeParser->parseString($time);

		$this->dispatchInBackground($event, $timestamp);
	}

	/**
	 * @param string $job_id
	 */
	public function deleteJob($job_id) {
		$this->messageQueueGateway->deleteEvent($job_id, EggTimerEvent::DONE);
	}

	/**
	 * @return MessageQueueJob[]
	 */
	public function getJobs() {
		return $this->messageQueueGateway->getEventsByType(EggTimerEvent::DONE, $this->now());
	}
}
