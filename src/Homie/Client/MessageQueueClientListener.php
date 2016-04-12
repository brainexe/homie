<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Listener.MessageQueueClient")
 */
class MessageQueueClientListener
{

    use RedisTrait;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient.Local")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ExecuteCommandEvent::EXECUTE => 'handleExecuteEvent'
        ];
    }

    /**
     * @param ExecuteCommandEvent $event
     */
    public function handleExecuteEvent(ExecuteCommandEvent $event)
    {
        $output = $this->client->executeWithReturn($event->command);

        if ($event->returnNeeded) {
            $this->getRedis()->lpush(MessageQueueClient::RETURN_CHANNEL, $output);
        }
    }
}
