<?php

namespace Homie\Sensors\CompilerPass;

use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Sensors\Formatter\Formatter;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @CompilerPassAnnotation("CompilerPass.SensorFormatter")
 */
class SensorFormatter implements CompilerPassInterface
{

    const TAG = 'sensor_formatter';

    use FileCacheTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $sensorBuilder  = $container->getDefinition('SensorBuilder');
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        $formatter      = [];

        foreach (array_keys($taggedServices) as $serviceId) {
            /** @var Formatter $service */
            $service = $container->get($serviceId);

            $type = $service->getType();
            $formatter[] = $type;
            $sensorBuilder->addMethodCall(
                'addFormatter',
                [
                    $type,
                    new Reference($serviceId)
                ]
            );
        }

        $this->dumpVariableToCache('sensor_formatter', $formatter);
    }
}
