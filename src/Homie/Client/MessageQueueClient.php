<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
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
    public function execute($command)
    {
        $event = new ExecuteCommandEvent($command, false);

        $this->dispatchInBackground($event);
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithReturn($command)
    {
        $event = new ExecuteCommandEvent($command, true);

        $this->dispatchInBackground($event);

        return $this->getRedis()->brPop(self::RETURN_CHANNEL, self::TIMEOUT);
    }
}
