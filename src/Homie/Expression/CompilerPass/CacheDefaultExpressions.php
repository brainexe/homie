<?php

namespace Homie\Expression\CompilerPass;

use BrainExe\Core\Traits\FileCacheTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;

/**
 * @CompilerPassAnnotation
 */
class CacheDefaultExpressions implements CompilerPassInterface
{
    use FileCacheTrait;

    const CACHE_FILE = 'default_expressions.php';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $expressions = [];
        $serviceIds = $container->findTaggedServiceIds('default_expressions');
        foreach (array_keys($serviceIds) as $serviceId) {
            $definition = $container->getDefinition($serviceId);

            /** @var DefaultExpression $class */
            $class = $definition->getClass();
            foreach ($class::getDefaultExpressions() as $entity) {
                $expressions[$entity->expressionId] = $entity;
            }
        }

        $this->dumpVariableToCache(self::CACHE_FILE, $expressions);
    }
}
