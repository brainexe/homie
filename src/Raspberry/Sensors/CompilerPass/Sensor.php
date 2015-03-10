<?php

namespace Raspberry\Sensors\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Raspberry\Sensors\Interfaces\Sensor as SensorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPassAnnotation("CompilerPass.Sensor")
 */
class Sensor implements CompilerPassInterface
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

            $sensorBuilder->addMethodCall(
                'addSensor',
                [
                    $service->getSensorType(),
                    new Reference($serviceId)
                ]
            );
        }
    }
}
