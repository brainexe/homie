<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Expression.Variable", public=true)
 */
class Variable
{

    const REDIS_KEY = 'variable';

    use RedisTrait;

    /**
     * @param string $key
     * @param string $value
     */
    public function setVariable(string $key, string $value)
    {
        $this->getRedis()->hset(self::REDIS_KEY, $key, $value);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getVariable(string $key)
    {
        return $this->getRedis()->hget(self::REDIS_KEY, $key);
    }

    /**
     * @param string $key
     */
    public function deleteVariable(string $key)
    {
        $this->getRedis()->hdel(self::REDIS_KEY, $key);
    }

    /**
     * @return string[]
     */
    public function getAll()
    {
        return $this->getRedis()->hgetall(self::REDIS_KEY);
    }
}
