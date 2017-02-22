<?php

namespace Homie\Expression;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Homie\Expression\Listener\RebuildExpressionCache;

/**
 * @Service
 */
class Gateway
{
    use RedisTrait;
    use IdGeneratorTrait;
    use EventDispatcherTrait;

    const REDIS_KEY = 'expressions';

    /**
     * @var Language
     */
    private $language;

    /**
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @param Entity $entity
     * @param bool $updateCache
     */
    public function save(Entity $entity, bool $updateCache = true)
    {
        $entity->expressionId = $entity->expressionId ?: $this->generateUniqueId('expressionid');

        $entity->compiledCondition = $this->language->compile(
            implode(' && ', $entity->conditions),
            $this->language->getParameterNames()
        );

        $this->getRedis()->hset(
            self::REDIS_KEY,
            $entity->expressionId,
            serialize($entity)
        );

        if ($updateCache) {
            $this->updateCache();
        }
    }

    /**
     * @return Entity[]
     */
    public function getAll() : array
    {
        return array_map(
            function ($string) {
                return unserialize($string);
            },
            $this->getRedis()->hgetall(self::REDIS_KEY)
        );
    }

    /**
     * @param string[] $entityIds
     * @return Entity[]
     */
    public function getEntities(array $entityIds) : array
    {
        return array_map(
            function ($string) : Entity {
                return unserialize($string);
            },
            $this->getRedis()->hmget(self::REDIS_KEY, $entityIds)
        );
    }

    /**
     * @param string $expressionId
     * @return bool
     */
    public function delete(string $expressionId) : bool
    {
        $result = $this->getRedis()->hdel(self::REDIS_KEY, [$expressionId]);
        if ($result) {
            $this->updateCache();
        }

        return (bool)$result;
    }

    public function updateCache()
    {
        $event = new RebuildExpressionCache();
        $this->dispatchEvent($event);
    }
}
