<?php

namespace Raspberry\Sensors;

use BrainExe\Core\Annotations\CompilerPass;
use Raspberry\Sensors\Sensors\SensorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPass
 */
class SensorCompilerPass implements CompilerPassInterface
{

    const TAG = 'sensor';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $sensorBuilder  = $container->getDefinition('SensorBuilder');
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var SensorInterface $service */
            $service = $container->get($serviceId);

            $sensorBuilder->addMethodCall('addSensor', [$service->getSensorType(), new Reference($serviceId)]);
        }
    }
}
