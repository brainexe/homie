<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\FileCacheTrait;

/**
 * @Service("Expression.Cache", public=false)
 */
class Cache
{

    use FileCacheTrait;

    const CACHE_FILE = 'expressions';
    const BASE       = "use \\BrainExe\\Core\\EventDispatcher\\AbstractEvent;
use \\Symfony\\Component\\DependencyInjection\\Container;
return function(AbstractEvent \$event, string \$eventName, Container \$container) {
%s
};";

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @Inject({
     *  "@Expression.Gateway",
     * })
     * @param Gateway $gateway
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function writeCache()
    {
        $all = $this->gateway->getAll();

        $content = '';
        foreach ($all as $entity) {
            if ($entity->compiledCondition && $entity->enabled) {
                $this->gateway->save($entity, false);
                $content .= sprintf(
                    "\t\t\$entity = %s;\n\t\tif (%s) {\n\t\t\tyield \$entity;\n\t\t}\n",
                    var_export($entity, true),
                    $entity->compiledCondition
                );
            }
        }

        $this->dumpCacheFile(self::CACHE_FILE, sprintf(self::BASE, $content));
    }
}
