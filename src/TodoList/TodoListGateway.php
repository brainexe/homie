<?php

namespace Homie\TodoList;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;
use Homie\TodoList\VO\TodoItemVO;

/**
 * @Service
 */
class TodoListGateway
{

    use RedisTrait;
    use TimeTrait;

    const TODO_KEY = 'todo:%d';
    const TODO_IDS = 'todo_ids';

    /**
     * @param TodoItemVO $itemVo
     */
    public function addItem(TodoItemVO $itemVo) : void
    {
        $redis = $this->getRedis();

        $raw = (array)$itemVo;

        $redis->hmset($this->getRedisKey($itemVo->todoId), $raw);

        $redis->sadd(self::TODO_IDS, [$itemVo->todoId]);
    }

    /**
     * @return array[]
     */
    public function getList() : array
    {
        $itemIds = $this->getRedis()->smembers(self::TODO_IDS);

        $redis = $this->getRedis()->pipeline();
        foreach ($itemIds as $itemId) {
            $redis->hgetall($this->getRedisKey($itemId));
        }

        return $redis->execute();
    }

    /**
     * @param int $itemId
     * @return array
     */
    public function getRawItem(int $itemId) : array
    {
        return $this->getRedis()->hgetall($this->getRedisKey($itemId));
    }

    /**
     * @param int $itemId
     * @param array $changes
     */
    public function editItem(int $itemId, array $changes) : void
    {
        $key = $this->getRedisKey($itemId);

        $changes['lastChange'] = $this->now();

        $this->getRedis()->hmset($key, $changes);
    }

    /**
     * @param int $itemId
     */
    public function deleteItem(int $itemId) : void
    {
        $key = $this->getRedisKey($itemId);

        $this->getRedis()->del($key);
        $this->getRedis()->srem(self::TODO_IDS, $itemId);
    }

    /**
     * @param int $itemId
     * @return string
     */
    private function getRedisKey(int $itemId) : string
    {
        return sprintf(self::TODO_KEY, $itemId);
    }
}
