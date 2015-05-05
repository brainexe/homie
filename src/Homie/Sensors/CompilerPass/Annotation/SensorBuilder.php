<?php

namespace Homie\Sensors\CompilerPass\Annotation;

use BrainExe\Annotations\Loader\Annotation\ServiceDefinitionBuilder;
use Homie\Sensors\CompilerPass\Sensor as CompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class SensorBuilder extends ServiceDefinitionBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->setPublic(false);
        $definition->addTag(CompilerPass::TAG);

        return [$serviceId, $definition];
    }
}
