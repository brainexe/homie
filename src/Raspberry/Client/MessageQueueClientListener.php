<?php

namespace Raspberry\Client;

use BrainExe\Core\Traits\RedisTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class MessageQueueClientListener implements EventSubscriberInterface
{

    use RedisTrait;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@RaspberryClient.Local")
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
            $this->getRedis()->lPush(MessageQueueClient::RETURN_CHANNEL, $output);
        }
    }
}
