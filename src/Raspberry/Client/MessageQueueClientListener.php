<?php

namespace Raspberry\Client;

use BrainExe\Core\EventDispatcher\AbstractEventListener;
use Redis;

/**
 * @EventListener
 */
class MessageQueueClientListener extends AbstractEventListener {

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
		/** @var LocalClient $local_client */
		$local_client = $this->getService('RaspberryClient.Local');

		$output = $local_client->executeWithReturn($event->command);

		if ($event->return_needed) {
			/** @var Redis $redis */
			$redis = $this->getService('redis');
			$redis->lPush(MessageQueueClient::RETURN_CHANNEL, $output);
		}
	}
}