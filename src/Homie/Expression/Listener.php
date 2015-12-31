<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\FileCacheTrait;
use Generator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @Service("Expression.Listener", public=false)
 */
class Listener extends EventDispatcher
{

    use FileCacheTrait;

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var Container
     */
    private $container;

    /**
     * @Inject({
     *     "@Expression.Gateway",
     *     "@Expression.Language",
     *     "@service_container",
     * })
     * @param Gateway $gateway
     * @param Language $language
     * @param Container $container
     */
    public function __construct(Gateway $gateway, Language $language, Container $container)
    {
        $this->gateway   = $gateway;
        $this->language  = $language;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        $cachedFunction = $this->includeFile(Cache::CACHE_FILE);
        if (!$cachedFunction) {
            return;
        }

        /** @var Generator|Entity[] $matches */
        $matches = call_user_func($cachedFunction, $event, $eventName, $this->container);
        foreach ($matches as $entity) {
            $parameters = [
                'event'     => $event,
                'eventName' => $eventName,
                'entity'    => $entity
            ];

            $oldParams = $entity->payload;
            foreach ($entity->actions as $action) {
                $this->language->evaluate($action, $parameters);
            }

            if ($entity->payload != $oldParams) {
                $this->gateway->save($entity);
            }
        }
    }
}
