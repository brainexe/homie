<?php

namespace Homie\Sensors\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Sensors\Interfaces\Sensor as SensorInterface;
use Homie\Sensors\SensorBuilder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @CompilerPassAnnotation
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

        $definitions = [];
        $references = [];
        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var SensorInterface $service */
            $reflection = new ReflectionClass($container->getDefinition($serviceId)->getClass());
            $service = $reflection->newInstanceWithoutConstructor();

            $type = $service->getSensorType();
            $references[$type]  = new ServiceClosureArgument(new Reference($serviceId));
            $definitions[$type] = (array)$service->getDefinition();
        }

        $sensorBuilder->setArguments([
            new Definition(ServiceLocator::class, [$references]),
            array_keys($definitions)
        ]);

        $this->dumpVariableToCache(
            'sensors',
            $definitions
        );
    }
}
