<?php

namespace Homie\TodoList;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(name="ShoppingListGateway", public=false)
 */
class Gateway
{
    use RedisTrait;

    const REDIS_KEY = 'shopping_list';

    /**
     * @return string[]
     */
    public function getItems() : array
    {
        return (array)$this->getRedis()->smembers(self::REDIS_KEY);
    }

    /**
     * @param string $name
     */
    public function addItem(string $name)
    {
        $this->getRedis()->sadd(self::REDIS_KEY, $name);
    }

    /**
     * @param string $name
     */
    public function removeItem(string $name)
    {
        $this->getRedis()->srem(self::REDIS_KEY, $name);
    }
}
