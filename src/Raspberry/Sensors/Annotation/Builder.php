<?php

namespace Raspberry\Sensors\Annotation;

use BrainExe\Annotations\Loader\Annotation\ServiceDefinitionBuilder;
use Raspberry\Sensors\CompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class Builder extends ServiceDefinitionBuilder
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