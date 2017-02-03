<?php

namespace Homie\Sensors\CompilerPass\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Sensors\CompilerPass\SensorFormatter as CompilerPass;
use Symfony\Component\DependencyInjection\Definition;

class FormatterBuilder extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function setupDefinition(Definition $definition, string $serviceId)
    {
        $definition->setPublic(false);
        $definition->addTag(CompilerPass::TAG);
    }
}
