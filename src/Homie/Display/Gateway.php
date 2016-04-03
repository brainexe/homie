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
        $setting->displayId = $this->generateUniqueId('displayid');
        $this->update($setting);
    }

    /**
     * @param Settings $setting
     */
    public function update(Settings $setting)
    {
        $this->getRedis()->hset(self::KEY, $setting->displayId, serialize($setting));
    }

    /**
     * @return Generator|Settings[]
     */
    public function getAll()
    {
        $displays = $this->getRedis()->hgetall(self::KEY);

        foreach ($displays as $screenId => $display) {
            yield $screenId => unserialize($display);
        }
    }

    /**
     * @param int $displayId
     * @return Settings
     */
    public function get($displayId)
    {
        $display = $this->getRedis()->hget(self::KEY, $displayId);

        return unserialize($display);
    }

    /**
     * @param int $displayId
     */
    public function delete($displayId)
    {
        $this->getRedis()->hdel(self::KEY, [$displayId]);
    }
}
