<?php

namespace Homie\Switches;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Gateway", public=false)
 */
class Gateway
{

    use RedisTrait;
    use IdGeneratorTrait;

    const REDIS_SWITCH     = 'switches:%s';
    const REDIS_SWITCH_IDS = 'switch:ids';

    /**
     * @return array[]
     */
    public function getAll()
    {
        $switchIds = $this->getIds();

        $pipeline = $this->getRedis()->pipeline();

        foreach ($switchIds as $switchId) {
            $pipeline->hgetall(self::getRedisKey($switchId));
        }

        return $pipeline->execute();
    }

    /**
     * @param int $switchId
     * @return array
     */
    public function get($switchId)
    {
        return $this->getRedis()->hgetall($this->getRedisKey($switchId));
    }

    /**
     * @return int[]
     */
    public function getIds()
    {
        return $this->getRedis()->smembers(self::REDIS_SWITCH_IDS);
    }

    /**
     * @param SwitchVO $switch
     * @return int new switch id
     */
    public function add(SwitchVO $switch)
    {
        $switch->switchId = $newId = $this->generateUniqueId('switchid');

        $pipeline = $this->getRedis()->pipeline();

        $key = $this->getRedisKey($newId);
        $pipeline->hmset($key, (array)$switch);

        $this->getRedis()->sadd(self::REDIS_SWITCH_IDS, [$newId]);

        $pipeline->execute();

        return $newId;
    }

    /**
     * @param SwitchVO $switch
     */
    public function edit(SwitchVO $switch)
    {
        $key = $this->getRedisKey($switch->switchId);

        $this->getRedis()->hmset($key, (array)$switch);
    }

    /**
     * @param int $switchId
     */
    public function delete($switchId)
    {
        $redis = $this->getRedis();

        $redis->srem(self::REDIS_SWITCH_IDS, $switchId);
        $redis->del(self::getRedisKey($switchId));
    }

    /**
     * @param integer $switchId
     * @return string
     */
    private function getRedisKey($switchId)
    {
        return sprintf(self::REDIS_SWITCH, $switchId);
    }
}
