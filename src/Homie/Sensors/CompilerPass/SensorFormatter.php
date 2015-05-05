<?php

namespace Homie\Sensors\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Interfaces\Sensor as SensorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPassAnnotation("CompilerPass.SensorFormatter")
 */
class SensorFormatter implements CompilerPassInterface
{

    const TAG = 'sensor_formatter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $sensorBuilder  = $container->getDefinition('SensorBuilder');
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var Formatter $service */
            $service = $container->get($serviceId);

            $sensorBuilder->addMethodCall(
                'addFormatter',
                [
                    $service->getType(),
                    new Reference($serviceId)
                ]
            );
        }
    }
}
