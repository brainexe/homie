<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Homie\Expression\Listener\RebuildExpressionCache;

/**
 * @Service("Expression.Gateway", public=false)
 */
class Gateway
{

    const REDIS_KEY = 'expressions';

    /**
     * @var Language
     */
    private $language;

    use RedisTrait;
    use IdGeneratorTrait;
    use EventDispatcherTrait;

    /**
     * @Inject("@Expression.Language")
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @param Entity $entity
     */
    public function save(Entity $entity)
    {
        $entity->expressionId = $entity->expressionId ?: $this->generateUniqueId('expressionid');
        $entity->payload      = $entity->payload ?: [];

        $entity->compiledCondition = $this->language->compile(
            implode(' && ', $entity->conditions),
            $this->language->getParameterNames()
        );

        $this->getRedis()->hset(
            self::REDIS_KEY,
            $entity->expressionId,
            serialize($entity)
        );

        $this->updateCache();
    }

    /**
     * @return Entity[]
     */
    public function getAll()
    {
        return array_map(
            function ($string) {
                return unserialize($string);
            },
            $this->getRedis()->hgetall(self::REDIS_KEY)
        );
    }

    /**
     * @param int[] $entityIds
     * @return Entity[]
     */
    public function getEntities(array $entityIds)
    {
        return array_map(
            function ($string) {
                return unserialize($string);
            },
            $this->getRedis()->hmget(self::REDIS_KEY, $entityIds)
        );
    }

    /**
     * @param int $expressionId
     * @return bool
     */
    public function delete($expressionId)
    {
        $result = $this->getRedis()->hdel(self::REDIS_KEY, [$expressionId]);
        if ($result) {
            $this->updateCache();
        }

        return (bool)$result;
    }

    private function updateCache()
    {
        $event = new RebuildExpressionCache();
        $this->dispatchEvent($event);
    }
}
