<?php

namespace Homie\Expression\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Expression\CompilerPass\RegisterProvider;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class ExpressionLanguageBuilder extends ServiceDefinition
{

    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->setPublic(false); // TODO wip
        $definition->addTag(RegisterProvider::TAG);

        return [$serviceId, $definition];
    }
}
