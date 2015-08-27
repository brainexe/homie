<?php

namespace Homie\Node;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Node.Gateway", public=false)
 */
class Gateway
{

    const REDIS_KEY = 'nodes';

    use RedisTrait;

    /**
     * @return array[]
     */
    public function getAll()
    {
        return $this->getRedis()->hgetall(self::REDIS_KEY);
    }

    /**
     * @param int $nodeId
     * @param array $data
     * @return int
     */
    public function set($nodeId, array $data)
    {
        $this->getRedis()->hset(self::REDIS_KEY, $nodeId, json_encode($data));
    }

    /**
     * @param int $nodeId
     */
    public function delete($nodeId)
    {
        $this->getRedis()->hdel(self::REDIS_KEY, [$nodeId]);
    }
}
