<?php

namespace Homie\Switches;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

use Homie\Switches\VO\SwitchVO;

/**
 * @Service
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
    public function getAll() : array
    {
        $switchIds = $this->getIds();

        $pipeline = $this->getRedis()->pipeline();

        foreach ($switchIds as $switchId) {
            $pipeline->hgetall($this->getRedisKey($switchId));
        }

        return $pipeline->execute();
    }

    /**
     * @param int $switchId
     * @return array
     */
    public function get(int $switchId) : array
    {
        return $this->getRedis()->hgetall($this->getRedisKey($switchId));
    }

    /**
     * @return int[]
     */
    public function getIds() : array
    {
        return $this->getRedis()->smembers(self::REDIS_SWITCH_IDS);
    }

    /**
     * @param SwitchVO $switch
     * @return int new switch id
     */
    public function add(SwitchVO $switch) : int
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
    public function edit(SwitchVO $switch) : void
    {
        $key = $this->getRedisKey($switch->switchId);

        $this->getRedis()->hmset($key, (array)$switch);
    }

    /**
     * @param int $switchId
     */
    public function delete(int $switchId)
    {
        $redis = $this->getRedis();

        $redis->srem(self::REDIS_SWITCH_IDS, $switchId);
        $redis->del([
            $this->getRedisKey($switchId)
        ]);
    }

    /**
     * @param int $switchId
     * @return string
     */
    private function getRedisKey(int $switchId)
    {
        return sprintf(self::REDIS_SWITCH, $switchId);
    }
}
