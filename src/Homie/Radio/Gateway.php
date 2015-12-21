<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

use Homie\Radio\VO\SwitchVO;

/**
 * @Service("Switch.Gateway", public=false)
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
            $pipeline->HGETALL(self::getRedisKey($switchId));
        }

        return $pipeline->execute();
    }

    /**
     * @param int $switchId
     * @return array
     */
    public function get($switchId)
    {
        return $this->getRedis()->HGETALL($this->getRedisKey($switchId));
    }

    /**
     * @return int[]
     */
    public function getIds()
    {
        return $this->getRedis()->SMEMBERS(self::REDIS_SWITCH_IDS);
    }

    /**
     * @param SwitchVO $switch
     * @return int new switch id
     */
    public function add(SwitchVO $switch)
    {
        $switch->switchId = $newId = $this->generateUniqueId();

        $pipeline = $this->getRedis()->pipeline();

        $key = $this->getRedisKey($newId);
        $pipeline->HMSET($key, (array)$switch);

        $this->getRedis()->SADD(self::REDIS_SWITCH_IDS, [$newId]);

        $pipeline->execute();

        return $newId;
    }

    /**
     * @param SwitchVO $switch
     */
    public function edit(SwitchVO $switch)
    {
        $key = $this->getRedisKey($switch->switchId);

        $this->getRedis()->hMset($key, (array)$switch);
    }

    /**
     * @param int $switchId
     */
    public function delete($switchId)
    {
        $redis = $this->getRedis();

        $redis->SREM(self::REDIS_SWITCH_IDS, $switchId);
        $redis->DEL(self::getRedisKey($switchId));
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
