<?php

namespace Raspberry\Notification;

use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Notification\NotificationCollectorInterface;
use Matze\Core\Traits\RedisTrait;

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