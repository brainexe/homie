<?php

namespace Homie\Node;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;
use Exception;
use Homie\Node;

/**
 * @Service("Node.Gateway", public=false)
 */
class Gateway
{

    const REDIS_KEY = 'nodes';

    use RedisTrait;

    /**
     * @return Node[]
     */
    public function getAll()
    {
        return array_map('unserialize', $this->getRedis()->hgetall(self::REDIS_KEY));
    }

    /**
     * @param int $nodeId
     * @return Node
     * @throws Exception
     */
    public function get($nodeId)
    {
        $raw = $this->getRedis()->hget(self::REDIS_KEY, $nodeId);
        if (!$raw) {
            throw new Exception(sprintf('Invalid node: %s', $nodeId));
        }

        return unserialize($raw);
    }
    /**
     * @param Node $node
     * @return int
     */
    public function save(Node $node)
    {
        $this->getRedis()->hset(self::REDIS_KEY, $node->getNodeId(), serialize($node));
    }

    /**
     * @param int $nodeId
     * @return int
     */
    public function delete($nodeId)
    {
        return $this->getRedis()->hdel(self::REDIS_KEY, [$nodeId]);
    }
}
