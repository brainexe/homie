<?php

namespace Raspberry\Notification;

use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Notification\NotificationCollectorInterface;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class MessageQueueNotifications implements NotificationCollectorInterface {

	use RedisTrait;

	/**
	 * @var MessageQueueGateway
	 */
	private $_message_queue_gateway;

	/**
	 * @Inject("@MessageQueueGateway")
	 */
	public function setMessageQueueGateway(MessageQueueGateway $message_queue_gateway) {
		$this->_message_queue_gateway = $message_queue_gateway;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNotification() {
		return $this->_message_queue_gateway->countEventsInFuture();
	}

}