<?php

namespace Homie\Expression;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Traits\FileCacheTrait;
use Generator;
use Homie\Expression\Event\EvaluateEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

/**
 * @Service
 */
class Listener extends SymfonyEventDispatcher
{

    use FileCacheTrait;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var callable
     */
    private $cachedFunctions;

    /**
     * @Inject({
     *     "container" = "@service_container"
     * })
     *
     * @param EventDispatcher $dispatcher
     * @param Container $container
     */
    public function __construct(
        EventDispatcher $dispatcher,
        Container $container
    ) {
        $this->dispatcher = $dispatcher;
        $this->container  = $container;

        $this->cachedFunctions = $this->includeFile(Cache::CACHE_FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (!$this->cachedFunctions) {
            return;
        }

        $this->handleEvent($eventName, $event);
    }

    /**
     * @param string $eventName
     * @param Event $event
     */
    private function handleEvent(string $eventName, Event $event)
    {
        /** @var Generator|Entity[] $matches */
        $matches = call_user_func($this->cachedFunctions, $event, $eventName, $this->container);
        foreach ($matches as $entity) {
            $parameters = [
                'event'     => $event,
                'eventName' => $eventName,
            ];

            foreach ($entity->actions as $action) {
                $evaluateEvent = new EvaluateEvent($action, $parameters);
                $this->dispatcher->dispatchEvent($evaluateEvent);
            }
        }
    }

    /**
     * @param callable $cachedFunctions
     */
    public function setCachedFunctions($cachedFunctions)
    {
        $this->cachedFunctions = $cachedFunctions;
    }
}
