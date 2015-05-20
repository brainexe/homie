<?php

namespace Homie\Sensors\CompilerPass\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Sensors\CompilerPass\SensorFormatter as CompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class FormatterBuilder extends ServiceDefinition
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
