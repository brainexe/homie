<?php

namespace Homie\Sensors\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Sensors\Interfaces\Sensor as SensorInterface;
use Homie\Sensors\SensorBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPassAnnotation("CompilerPass.Sensor")
 */
class Sensor implements CompilerPassInterface
{

    const TAG = 'sensor';

    use FileCacheTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $sensorBuilder  = $container->getDefinition('SensorBuilder');
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $sensors = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var SensorInterface $service */
            $service = $container->get($serviceId);

            $type = $service->getSensorType();
            $sensors[$type] = $service->getDefinition();
            $sensorBuilder->addMethodCall(
                'addSensor',
                [
                    $type,
                    new Reference($serviceId)
                ]
            );
        }

        $sensors = json_encode($sensors);
        $this->dumpVariableToCache('sensors', json_decode($sensors, true));
    }
}
