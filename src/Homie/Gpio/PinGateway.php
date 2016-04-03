<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class PinGateway
{
    const REDIS_PINS = 'pins';

    use RedisTrait;

    /**
     * @return string[]
     */
    public function getPinDescriptions()
    {
        $redis = $this->getRedis();

        return $redis->hgetall(self::REDIS_PINS);
    }

    /**
     * @param int $pinId
     * @param $description
     */
    public function setDescription($pinId, $description)
    {
        $this->getRedis()->hset(self::REDIS_PINS, $pinId, $description);
    }
}
