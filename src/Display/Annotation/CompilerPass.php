<?php

namespace Homie\Display\Annotation;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Homie\Display\Devices\DeviceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPassAnnotation
 */
class CompilerPass implements CompilerPassInterface
{

    const TAG = 'display_device';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $factory  = $container->findDefinition('Display.Devices.Factory');
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $displays = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var DeviceInterface $class */
            $class = $container->findDefinition($serviceId)->getClass();
            $displays[$class::getType()] = new Reference($serviceId);
        }

        $factory->setArguments([$displays]);
    }
}
