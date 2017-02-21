<?php

namespace Homie\Sensors\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Sensors\CompilerPass\Sensor as CompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class SensorBuilder extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->addTag(CompilerPass::TAG);
        $definition->setShared(false);
    }
}
