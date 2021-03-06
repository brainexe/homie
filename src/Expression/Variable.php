<?php

namespace Homie\Expression;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\RedisTrait;
use Homie\Expression\Event\VariableChangedEvent;

/**
 * @Service
 */
class Variable
{

    const REDIS_KEY = 'expression_variable';

    use EventDispatcherTrait;
    use RedisTrait;

    /**
     * @param string $key
     * @param string $value
     */
    public function setVariable(string $key, string $value) : void
    {
        $this->getRedis()->hset(self::REDIS_KEY, $key, $value);

        $event = new VariableChangedEvent(VariableChangedEvent::CHANGED, $key, $value);
        $this->dispatchEvent($event);
    }
    /**
     * @param string $key
     * @param integer $value
     */
    public function increaseVariable(string $key, integer $value) : void
    {
        $this->getRedis()->hincrby(self::REDIS_KEY, $key, $value);

        $event = new VariableChangedEvent(VariableChangedEvent::CHANGED, $key, $value);
        $this->dispatchEvent($event);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getVariable(string $key) : ?string
    {
        return $this->getRedis()->hget(self::REDIS_KEY, $key);
    }

    /**
     * @param string $key
     */
    public function deleteVariable(string $key) : void
    {
        $this->getRedis()->hdel(self::REDIS_KEY, [$key]);

        $event = new VariableChangedEvent(VariableChangedEvent::DELETED, $key);
        $this->dispatchEvent($event);
    }

    /**
     * @return string[]
     */
    public function getAll() : array
    {
        return $this->getRedis()->hgetall(self::REDIS_KEY);
    }
}
