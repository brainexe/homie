<?php

namespace Homie\Gpio;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service
 */
class PinGateway
{
    const REDIS_PINS = 'pins';

    use RedisTrait;

    /**
     * @return string[]
     */
    public function getPinDescriptions() : array
    {
        $redis = $this->getRedis();

        return $redis->hgetall(self::REDIS_PINS);
    }

    /**
     * @param int $pinId
     * @param $description
     */
    public function setDescription(int $pinId, string $description)
    {
        $this->getRedis()->hset(self::REDIS_PINS, $pinId, $description);
    }
}
