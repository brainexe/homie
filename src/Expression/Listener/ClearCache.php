<?php

namespace Homie\Expression\Listener;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Expression\Cache;
use Homie\Expression\CompilerPass\CacheDefaultExpressions;
use Homie\Expression\Gateway;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class ClearCache implements EventSubscriberInterface
{
    use FileCacheTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @Inject({
     *     "logger" = "@logger"
     * })
     * @param Cache $cache
     * @param Gateway $gateway
     * @param Logger $logger
     */
    public function __construct(
        Cache $cache,
        Gateway $gateway,
        Logger $logger
    ) {
        $this->cache   = $cache;
        $this->gateway = $gateway;
        $this->logger  = $logger;
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

            $this->gateway->save($entity, false);
            $this->logger->debug(sprintf('Registered entity "%s"', $expressionId));
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
