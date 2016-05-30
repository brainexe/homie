<?php

namespace Homie\Expression\Listener;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Expression\Language;
use ReflectionFunction;

/**
 * @EventListener("Expression.Listener.WriteFunction")
 */
class WriteFunctionCache
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

    /**
     * @Listen(ClearCacheEvent::NAME)
     */
    public function rebuildCache()
    {
        $functions = [];
        foreach ($this->language->getFunctions() as $name => $function) {
            $reflection = new ReflectionFunction($function['evaluator']);

            $functions[$name] = [
                'parameters' => array_merge(
                    $this->getParameters($reflection)
                ),
                'isAction'  => true, // TODO implement somehow
                'isTrigger' => true  // TODO implement somehow
            ];
        }

        ksort($functions);

        $this->dumpVariableToCache(self::CACHE, $functions);
    }

    /**
     * @param ReflectionFunction $reflection
     * @return array
     */
    private function getParameters(ReflectionFunction $reflection) : array
    {
        $parameters = [];
        foreach ($reflection->getParameters() as $i => $parameter) {
            if ($i >= 1) {
                $type         = $parameter->getType();
                $parameters[] = [
                    'name' => $parameter->getName(),
                    'type' => $type ? $type->__toString() : '',
                ];
            }
        }

        return $parameters;
    }
}
