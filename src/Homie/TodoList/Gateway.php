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
    public function getItems()
    {
        return $this->getRedis()->sMembers(self::REDIS_KEY);
    }

    /**
     * @param string $name
     */
    public function addItem($name)
    {
        $this->getRedis()->sAdd(self::REDIS_KEY, $name);
    }

    /**
     * @param string $name
     */
    public function removeItem($name)
    {
        $this->getRedis()->sRem(self::REDIS_KEY, $name);
    }
}
