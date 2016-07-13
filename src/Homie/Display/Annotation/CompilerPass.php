<?php

namespace Homie\Display\Annotation;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Homie\Display\Devices\DeviceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPassAnnotation("Display.Annotation.CompilerPass")
 */
class CompilerPass implements CompilerPassInterface
{

    const TAG = 'display_device';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $factory  = $container->getDefinition('Display.Devices.Factory');
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $displays = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var DeviceInterface $class */
            $class = $container->getDefinition($serviceId)->getClass();
            $displays[$class::getType()] = new Reference($serviceId);
        }

        $factory->setArguments([$displays]);
    }
}
