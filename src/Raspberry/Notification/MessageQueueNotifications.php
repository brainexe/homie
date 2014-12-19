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
     * @param MessageQueueGateway $messageQueueGateway
     */
    public function setMessageQueueGateway(MessageQueueGateway $messageQueueGateway)
    {
        $this->messageQueueGateway = $messageQueueGateway;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotification()
    {
        return $this->messageQueueGateway->countJobs(); //TODO in future
    }
}
