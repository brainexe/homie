<?php

namespace Raspberry\Gpio;

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
     * @return array[]
     */
    public function getPinDescriptions()
    {
        $redis = $this->getRedis();

        return $redis->hGetAll(self::REDIS_PINS);
    }
}
