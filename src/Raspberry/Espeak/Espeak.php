<?php

namespace Raspberry\Espeak;

use BrainExe\Core\MessageQueue\MessageQueueGateway;
use BrainExe\Core\MessageQueue\MessageQueueJob;
use Raspberry\Client\ClientInterface;

/**
 * @Service
 */
class Espeak implements SpeakOutputInterface {

	const DEFAULT_SPEAKER = 'de+m1';

	/**
	 * @var ClientInterface
	 */
	private $_raspberry_client;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;

	/**
	 * @Inject({"@MessageQueueGateway", "@RaspberryClient"})
	 */
	public function __construct(MessageQueueGateway $message_queue_gateway, ClientInterface $client) {
		$this->_raspberry_client = $client;
		$this->_message_queue_gateway = $message_queue_gateway;
	}

	/**
	 * @return array
	 */
	public function getSpeakers() {
		return ['de+m1' => 'DE Male', 'de+f1' => 'DE Female', 'en' => 'EN', 'fr' => 'FR'];
	}

	/**
	 * @return MessageQueueJob[]
	 */
	public function getPendingJobs() {
		return $this->_message_queue_gateway->getEventsByType(EspeakEvent::SPEAK, time());
	}

	/**
	 * @param string $text
	 * @param integer $volume
	 * @param integer $speed
	 * @param string $speaker
	 */
	public function speak($text, $volume = 100, $speed = 100, $speaker = self::DEFAULT_SPEAKER) {
		if (empty($text)) {
			return;
		}

		$command = sprintf('espeak "%s" -s %d -a %d  -v%ss --stdout | aplay', $text, $speed, $volume, $speaker);

		$this->_raspberry_client->execute($command);
	}

	/**
	 * @param string $job_id
	 */
	public function deleteJob($job_id) {
		$this->_message_queue_gateway->deleteEvent($job_id, EspeakEvent::SPEAK);
	}
} 