<?php

namespace Homie\Dashbaord\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Dashboard\WidgetCompilerPass as CompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class WidgetBuilder extends ServiceDefinition
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

        $serviceId = sprintf('__widget.%s', $serviceId);
        return [$serviceId, $definition];
    }
}
