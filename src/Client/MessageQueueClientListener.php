<?php

namespace Homie\Client;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\Traits\RedisTrait;
use Homie\Client\Adapter\MessageQueueClient;

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
     * @Listen(ExecuteCommandEvent::EXECUTE)
     * @param ExecuteCommandEvent $event
     */
    public function handleExecuteEvent(ExecuteCommandEvent $event)
    {
        if ($event->isReturnNeeded()) {
            $output = $this->client->executeWithReturn(
                $event->getCommand(),
                $event->getArguments()
            );

            $this->getRedis()->lpush(MessageQueueClient::RETURN_CHANNEL, $output);
        } else {
            $this->client->execute(
                $event->getCommand(),
                $event->getArguments()
            );
        }
    }
}
