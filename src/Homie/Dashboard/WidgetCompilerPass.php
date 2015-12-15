<?php

namespace Homie\Dashboard;

use BrainExe\Core\Annotations\CompilerPass;
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
        $widgets = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var WidgetInterface $class */
            $class = $container->getDefinition($serviceId)->getClass();
            $widgets[$class::TYPE] = new Reference($serviceId);
        }

        $definition->addMethodCall('setWidgets', $widgets);

    }
}
