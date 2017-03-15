<?php

namespace Homie\Expression;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\FileCacheTrait;

/**
 * @Service
 */
class Cache
{

    use FileCacheTrait;

    const CACHE_FILE = 'expressions';
    const BASE       = "\nuse \\BrainExe\\Core\\EventDispatcher\\AbstractEvent;\n
use \\Symfony\\Component\\DependencyInjection\\Container;
return function(AbstractEvent \$event, string \$eventName, Container \$container) {
%s
};";

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function writeCache() : void
    {
        $all = $this->gateway->getAll();

        $content = '';
        foreach ($all as $entity) {
            if ($entity->compiledCondition && $entity->enabled) {
                $this->gateway->save($entity, false);
                $content .= sprintf(
                    "\tif (%s) {\n\t\tyield %s;\n\t}\n",
                    $entity->compiledCondition,
                    var_export($entity, true)
                );
            }
        }

        if (empty($content)) {
            $content = "\treturn [];";
        }

        $this->dumpCacheFile(self::CACHE_FILE, sprintf(self::BASE, $content));
    }
}
