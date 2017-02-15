<?php

namespace Homie\Sensors\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Sensors\Interfaces\Sensor as SensorInterface;
use Homie\Sensors\SensorBuilder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $sensorBuilder  = $container->getDefinition(SensorBuilder::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $sensors = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var SensorInterface $service */
            $reflection = new ReflectionClass($container->getDefinition($serviceId)->getClass());
            $service = $reflection->newInstanceWithoutConstructor();

            $type = $service->getSensorType();
            $sensors[$type] = $service->getDefinition();
            $sensorBuilder->addMethodCall(
                'addSensor',
                [
                    $type,
                    $serviceId
                ]
            );
        }

        $sensors = json_encode($sensors);
        $this->dumpVariableToCache('sensors', json_decode($sensors, true));
    }
}
