<?php

namespace Raspberry\DIC;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class WidgetCompilerPass implements CompilerPassInterface
{

    const TAG = 'widget';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /** @var Definition $definition */
        $definition = $container->getDefinition('WidgetFactory');

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach (array_keys($taggedServices) as $serviceId) {
            $definition->addMethodCall('addWidget', [new Reference($serviceId)]);
        }
    }
}
