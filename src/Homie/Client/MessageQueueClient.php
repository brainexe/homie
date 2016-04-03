<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @todo
 * @Service("MessageQueueClient.Local", public=false)
 */
class MessageQueueClient implements ClientInterface
{
    use RedisTrait;
    use EventDispatcherTrait;

    const RETURN_CHANNEL = 'return_channel';
    const TIMEOUT        = 10;

    /**
     * {@inheritdoc}
     */
    public function execute($command, array $arguments = [])
    {
        $event = new ExecuteCommandEvent($command, $arguments, false);

        $this->dispatchInBackground($event);
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithReturn($command, array $arguments = [])
    {
        $event = new ExecuteCommandEvent($command, $arguments, true);

        $this->dispatchInBackground($event);

        return $this->getRedis()->brpop(self::RETURN_CHANNEL, self::TIMEOUT);
    }
}
