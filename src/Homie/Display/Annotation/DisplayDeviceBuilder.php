<?php

namespace Homie\Display\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class DisplayDeviceBuilder extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list ($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->setPublic(false);
        $definition->addTag(CompilerPass::TAG);

        return [$serviceId, $definition];
    }
}
