<?php

namespace Raspberry\Client;

use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class MessageQueueClientListener implements EventSubscriberInterface {

	use RedisTrait;

	/**
	 * @var LocalClient
	 */
	private $_client;

	/**
	 * @inject("@RaspberryClient.Local")
	 * @param LocalClient $client
	 */
	public function __construct(LocalClient $client) {
		$this->_client = $client;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			ExecuteCommandEvent::EXECUTE => 'handleExecuteEvent'
		];
	}

	/**
	 * @param ExecuteCommandEvent $event
	 */
	public function handleExecuteEvent(ExecuteCommandEvent $event) {
		$output = $this->_client->executeWithReturn($event->command);

		if ($event->return_needed) {
			$this->getRedis()->lPush(MessageQueueClient::RETURN_CHANNEL, $output);
		}
	}
}