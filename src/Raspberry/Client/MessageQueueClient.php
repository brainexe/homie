<?php

namespace Raspberry\Client;

use Matze\Core\Traits\EventDispatcherTrait;
use Matze\Core\Traits\RedisTrait;
use Symfony\Component\Process\Process;

/**
 * @Service("MessageQueueClient.Local", public=false)
 */
class MessageQueueClient implements ClientInterface {
	use RedisTrait;
	use EventDispatcherTrait;

	const RETURN_CHANNEL = 'return_channel';

	/**
	 * {@inheritdoc}
	 */
	public function execute($command) {
		$event = new ExecuteCommandEvent($command, false);

		$this->dispatchInBackground($event);
	}

	/**
	 * {@inheritdoc}
	 */
	public function executeWithReturn($command) {
		$event = new ExecuteCommandEvent($command, true);

		$this->dispatchInBackground($event);

		return $this->getRedis()->brPop(self::RETURN_CHANNEL, 5);
	}
}