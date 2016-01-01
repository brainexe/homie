<?php

namespace Homie\Expression\Listener;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\FileCacheTrait;
use BrainExe\Core\Traits\LoggerTrait;
use Homie\Expression\Cache;
use Homie\Expression\CompilerPass\CacheDefaultExpressions;
use Homie\Expression\Gateway;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Expression.Listener.ClearCache")
 */
class ClearCache implements EventSubscriberInterface
{
    use FileCacheTrait;
    use LoggerTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @Inject({
     *     "@Expression.Cache",
     *     "@Expression.Gateway"
     * })
     * @param Cache $cache
     * @param Gateway $gateway
     */
    public function __construct(Cache $cache, Gateway $gateway)
    {
        $this->cache   = $cache;
        $this->gateway = $gateway;
    }

    public static function getSubscribedEvents()
    {
        return [
            ClearCacheEvent::NAME           => 'rebuildCache',
            RebuildExpressionCache::REBUILD => 'rebuildCache'
        ];
    }

    public function rebuildCache()
    {
        $this->cache->writeCache();

        $this->installDefaultExpressions();
    }

    private function installDefaultExpressions()
    {
        $expressions = $this->includeFile(CacheDefaultExpressions::CACHE_FILE);
        if (!$expressions) {
            return;
        }

        $existing = $this->getExisting();
        foreach ($expressions as $expressionId => $entity) {
            if (isset($existing[$expressionId])) {
                continue;
            }

            $this->gateway->save($entity);
            $this->debug(sprintf('Registered entity "%s"', $expressionId));
        }
    }

    /**
     * @return bool[]
     */
    private function getExisting()
    {
        $existing = [];
        foreach ($this->gateway->getAll() as $entity) {
            $existing[$entity->expressionId] = true;
        };

        return $existing;
    }
}
