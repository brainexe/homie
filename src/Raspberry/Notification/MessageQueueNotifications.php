<?php

namespace Raspberry\Notification;

use BrainExe\MessageQueue\MessageQueueGateway;
use BrainExe\Core\Notification\NotificationCollectorInterface;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class MessageQueueNotifications implements NotificationCollectorInterface
{

    use RedisTrait;

    /**
     * @var MessageQueueGateway
     */
    private $messageQueueGateway;

    /**
     * @Inject("@MessageQueueGateway")
     * @param MessageQueueGateway $message_queue_gateway
     */
    public function setMessageQueueGateway(MessageQueueGateway $message_queue_gateway)
    {
        $this->messageQueueGateway = $message_queue_gateway;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification()
    {
        return $this->messageQueueGateway->countJobs(); //TODO in future
    }
}
