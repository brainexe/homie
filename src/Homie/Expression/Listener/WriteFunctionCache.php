<?php

namespace Homie\Expression\Listener;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Expression\Language;
use ReflectionFunction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Expression.Listener.WriteFunction")
 */
class WriteFunctionCache implements EventSubscriberInterface
{
    const CACHE = 'expression_functions';

    use FileCacheTrait;

    /**
     * @var Language
     */
    private $language;

    /**
     * @Inject({
     *     "@Expression.Language",
     * })
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public static function getSubscribedEvents()
    {
        return [
            ClearCacheEvent::NAME           => 'rebuildCache',
        ];
    }

    public function rebuildCache()
    {
        $functions = [];
        foreach ($this->language->getFunctions() as $name => $function) {
            $reflection = new ReflectionFunction($function['evaluator']);

            $parameters = [];
            foreach ($reflection->getParameters() as $i => $parameter) {
                if ($i >= 1) {
                    $parameters[] = $parameter->getName();
                }
            }
            $functions[$name] = $parameters;
        }

        $this->dumpVariableToCache(self::CACHE, $functions);
    }
}
