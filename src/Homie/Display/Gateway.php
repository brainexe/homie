<?php

namespace Homie\Display;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Generator;

/**
 * @Service("Display.Gateway", public=false)
 */
class Gateway
{
    const KEY = 'displays';

    use RedisTrait;
    use IdGeneratorTrait;

    /**
     * @param Settings $setting
     */
    public function addDisplay(Settings $setting)
    {
        $setting->displayId = $this->generateRandomNumericId();

        $this->getRedis()->hset(self::KEY, $setting->displayId, serialize($setting));
    }

    /**
     * @return Generator|Settings[]
     */
    public function getall()
    {
        $displays = $this->getRedis()->hgetall(self::KEY);

        foreach ($displays as $screenId => $display) {
            yield $screenId => unserialize($display);
        }
    }

    /**
     * @param int $displayId
     */
    public function delete($displayId)
    {
        $this->getRedis()->hdel(self::KEY, $displayId);
    }

}
