<?php

namespace Homie\Arduino\Device;


use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;
use Homie\Arduino\Device;
use Homie\Arduino\SerialEvent;

/**
 * @Service("Arduino.Device.Redis", public=false)
 */
class Redis implements Device
{

    const REDIS_CHANNEL = 'arduino';

    use RedisTrait;

    /**
     * @param SerialEvent $event
     */
    public function sendSerial(SerialEvent $event)
    {
        $line = sprintf(
            "%s:%d:%d",
            $event->getAction(),
            $event->getPin(),
            $event->getValue()
        );

        $this->getRedis()->publish(self::REDIS_CHANNEL, $line);
    }
}
